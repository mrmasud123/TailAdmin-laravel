<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function create(array $data, User $createdBy): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $createdBy) {

            [$itemsData, $subTotal] = $this->prepareItems($data['items']);

            $discountAmount = round((float) ($data['discount_amount'] ?? 0), 2);
            $taxAmount = round((float) ($data['tax_amount'] ?? 0), 2);
            $totalAmount = round($subTotal - $discountAmount + $taxAmount, 2);

            $status = ($data['action'] ?? 'draft') === 'send' ? 'sent' : 'draft';

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePoNumber(),
                'supplier_id' => $data['supplier_id'],
                'status' => $status,
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy->id,
            ]);

            $purchaseOrder->items()->createMany($itemsData);

            return $purchaseOrder->load('items');
        });
    }

    public function update(array $data, User $updatedBy): PurchaseOrder
    {
        $purchaseOrder = PurchaseOrder::find($data['purchase_order_id']);

        if (! $purchaseOrder) {
            throw new \InvalidArgumentException('Purchase order not found.');
        }

        return DB::transaction(function () use ($data, $updatedBy, $purchaseOrder) {

            [$itemsData, $subTotal] = $this->prepareItems($data['items']);

            $discountAmount = round((float) ($data['discount_amount'] ?? 0), 2);
            $taxAmount = round((float) ($data['tax_amount'] ?? 0), 2);
            $totalAmount = round($subTotal - $discountAmount + $taxAmount, 2);

            $purchaseOrder->update([
                'supplier_id' => $data['supplier_id'],
                'status' => $data['status'] ?? $purchaseOrder->status,
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
                'updated_at' => date(now())
            ]);

            $purchaseOrder->items()->delete();
            $purchaseOrder->items()->createMany($itemsData);

            return $purchaseOrder->load('items');
        });
    }
    private function prepareItems(array $items): array
    {
        $subTotal = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $qty = (float) $item['qty'];
            $cost = (float) $item['cost'];
            $lineTotal = round($qty * $cost, 2);

            $subTotal += $lineTotal;

            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity_ordered' => $qty,
                'unit_cost' => $cost,
                'discount' => 0,
                'tax' => 0,
                'total_price' => $lineTotal,
            ];
        }

        return [$itemsData, round($subTotal, 2)];
    }
    private function generatePoNumber(): string
    {
        $year = now()->year;
        $prefix = "PO-{$year}-";

        $lastNumber = DB::table('purchase_orders')
            ->where('po_number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('po_number');

        $nextSequence = 1;

        if ($lastNumber) {
            $lastSequence = (int) Str::afterLast($lastNumber, '-');
            $nextSequence = $lastSequence + 1;
        }

        return $prefix . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
    }
}
