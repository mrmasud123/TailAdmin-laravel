<?php
// app/Services/GoodsReceiptService.php
namespace App\Services;

use App\Models\{
    GoodsReceiptItem, GoodsReceiptNote, ProductBatch, PurchaseInvoice,
    PurchaseOrder, PurchaseOrderItem, StockMovement, SupplierPayment, Transaction
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoodsReceiptService
{
    public function store(array $data, int $userId): GoodsReceiptNote
    {
        return DB::transaction(function () use ($data, $userId) {

            $po = PurchaseOrder::where('id', $data['purchase_order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($po->status, ['sent', 'partially_received'])) {
                throw ValidationException::withMessages([
                    'purchase_order_id' => 'This purchase order is no longer receivable.',
                ]);
            }

            $isCompleting = $data['action'] === 'completed';

            $grn = GoodsReceiptNote::create([
                'grn_number' => $this->generateGrnNumber(),
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'received_date' => $data['received_date'],
                'status' => $isCompleting ? 'completed' : 'pending',
                'received_by' => $userId,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemPayload) {

                $poItem = PurchaseOrderItem::where('id', $itemPayload['purchase_order_item_id'])
                    ->where('purchase_order_id', $po->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $pending = $poItem->quantity_ordered - $poItem->quantity_received;

                $batches = collect($itemPayload['batches'])
                    ->filter(fn ($b) => (float) ($b['quantity'] ?? 0) > 0)
                    ->values();

                if ($batches->isEmpty()) {
                    continue;
                }

                $requestedTotal = $batches->sum(fn ($b) => (float) $b['quantity']);

                if ($requestedTotal > $pending) {
                    throw ValidationException::withMessages([
                        'items' => "Requested quantity for product ID {$poItem->product_id} ({$requestedTotal}) exceeds pending quantity ({$pending}).",
                    ]);
                }

                foreach ($batches as $batchPayload) {
                    $qty = (float) $batchPayload['quantity'];

                    $grnItem = GoodsReceiptItem::create([
                        'goods_receipt_note_id' => $grn->id,
                        'purchase_order_item_id' => $poItem->id,
                        'product_id' => $poItem->product_id,
                        'batch_no' => $batchPayload['batch_no'] ?? $this->generateBatchNo($poItem->product_id),
                        'manufacture_date' => $batchPayload['manufacture_date'] ?? null,
                        'expiry_date' => $batchPayload['expire_date'],
                        'quantity_received' => $qty,
                        'unit_cost' => $batchPayload['purchase_price'] ?? $poItem->unit_cost,
                    ]);

                    if ($isCompleting) {
                        $this->applyToStock($poItem, $grnItem, $batchPayload, $grn);
                        $poItem->increment('quantity_received', $qty);
                    }
                }
            }

            $invoice = null;

            if ($isCompleting) {
                $po->refresh()->load('items');
                $this->recalculatePoStatus($po);

                if ($po->fresh()->status === 'received') {
                    $invoice = $this->createInvoiceForPo($po, $userId);
                }

                if (!empty($data['paid_amount']) && (float) $data['paid_amount'] > 0) {
                    if (!$invoice) {
                        throw ValidationException::withMessages([
                            'paid_amount' => 'A payment can only be recorded once this receipt fully completes the purchase order and generates an invoice.',
                        ]);
                    }

                    $this->recordSupplierPayment($invoice, $data, $userId);
                }
            }

            return $grn;
        });
    }

    protected function createInvoiceForPo(PurchaseOrder $po, int $userId): PurchaseInvoice
    {
        return PurchaseInvoice::firstOrCreate(
            ['purchase_order_id' => $po->id],
            [
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_id' => $po->supplier_id,
                'invoice_date' => now()->toDateString(),
                'sub_total' => $po->sub_total,
                'discount_amount' => $po->discount_amount,
                'tax_amount' => $po->tax_amount,
                'total_amount' => $po->total_amount,
                'paid_amount' => 0,
                'due_amount' => $po->total_amount,
                'status' => 'unpaid',
                'created_by' => $userId,
            ]
        );
    }

    protected function recordSupplierPayment(PurchaseInvoice $invoice, array $data, int $userId): SupplierPayment
    {
        $amount = (float) $data['paid_amount'];

        if ($amount > (float) $invoice->due_amount) {
            throw ValidationException::withMessages([
                'paid_amount' => "Payment ({$amount}) exceeds the invoice due amount ({$invoice->due_amount}).",
            ]);
        }

        $payment = SupplierPayment::create([
            'purchase_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->supplier_id,
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_method' => $data['payment_method'] ?? 'cash',
            'reference_no' => $data['payment_reference_no'] ?? null,
            'notes' => $data['payment_notes'] ?? null,
            'created_by' => $userId,
        ]);

        Transaction::create([
            'supplier_id' => $invoice->supplier_id,
            'transactionable_type' => PurchaseInvoice::class,
            'transactionable_id' => $invoice->id,
            'type' => 'purchase_payment',
            'direction' => 'debit',
            'amount' => $amount,
            'payment_method' => $payment->payment_method,
            'reference_no' => $payment->reference_no,
            'note' => "Payment against invoice {$invoice->invoice_number}",
            'created_by' => $userId,
        ]);

        $newPaid = $invoice->paid_amount + $amount;
        $newDue = $invoice->total_amount - $newPaid;

        $invoice->update([
            'paid_amount' => $newPaid,
            'due_amount' => max($newDue, 0),
            'status' => $newDue <= 0 ? 'paid' : 'partial',
        ]);

        return $payment;
    }

    protected function applyToStock(
        PurchaseOrderItem $poItem,
        GoodsReceiptItem  $grnItem,
        array             $batchPayload,
        GoodsReceiptNote  $grn
    ): void {
        $qty = (float) $batchPayload['quantity'];
        $purchasePrice = $batchPayload['purchase_price'] ?? $poItem->unit_cost;
        $salesPrice = $batchPayload['sales_price'] ?? null;

        $query = ProductBatch::where('product_id', $poItem->product_id)
            ->where('expire_date', $batchPayload['expire_date']);

        if (!empty($batchPayload['batch_no'])) {
            $query->where('batch_no', $batchPayload['batch_no']);
        } else {
            $query->whereNull('batch_no')->where('supplier_id', $grn->supplier_id);
        }

        $batch = $query->lockForUpdate()->first();

        if (!$batch) {
            $batch = ProductBatch::create([
                'product_id' => $poItem->product_id,
                'supplier_id' => $grn->supplier_id,
                'batch_no' => $batchPayload['batch_no'] ?? $this->generateBatchNo($poItem->product_id),
                'expire_date' => $batchPayload['expire_date'],
                'purchase_price' => $purchasePrice,
                'sales_price' => $salesPrice ?? $purchasePrice,
                'quantity' => 0,
            ]);
        }

        $batch->increment('quantity', (int) round($qty));

        StockMovement::create([
            'product_id' => $poItem->product_id,
            'product_batch_id' => $batch->id,
            'type' => 'purchase',
            'quantity' => (int) round($qty),
            'unit_price' => $purchasePrice,
            'reference' => $grn->grn_number . ' / ' . ($grnItem->batch_no ?? 'no-batch'),
        ]);
    }

    protected function recalculatePoStatus(PurchaseOrder $po): void
    {
        $totalOrdered = $po->items->sum('quantity_ordered');
        $totalReceived = $po->items->sum('quantity_received');

        $po->update([
            'status' => $totalReceived >= $totalOrdered ? 'received' : 'partially_received',
        ]);
    }

    protected function generateGrnNumber(): string
    {
        $year = now()->year;
        $lastId = GoodsReceiptNote::withTrashed()->max('id') + 1;

        return sprintf('GRN-%d-%05d', $year, $lastId);
    }

    protected function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastId = PurchaseInvoice::withTrashed()->max('id') + 1;

        return sprintf('PINV-%d-%05d', $year, $lastId);
    }

    private function generateBatchNo(int $productId): string
    {
        return 'B-' . $productId . '-' . strtoupper(Str::random(5));
    }
}
