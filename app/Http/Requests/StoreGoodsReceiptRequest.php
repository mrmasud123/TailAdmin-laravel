<?php
// app/Http/Requests/Admin/StoreGoodsReceiptRequest.php

namespace App\Http\Requests;

use App\Models\PurchaseOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust to your policy/gate
    }

    public function rules(): array
    {
        return [
            'purchase_order_id'                         => ['required', 'exists:purchase_orders,id'],
            'received_date'                             => ['required', 'date'],
            'challan_no'                                => ['nullable', 'string', 'max:100'],
            'notes'                                     => ['nullable', 'string', 'max:1000'],
            'action'                                    => ['required', 'in:draft,completed'],
            'items'                                     => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id'            => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.batches'                           => ['required', 'array', 'min:1'],
            'items.*.batches.*.batch_no'                => ['nullable', 'string', 'max:100'],
            'items.*.batches.*.manufacture_date'        => ['nullable', 'date'],
            'items.*.batches.*.expire_date'             => ['required', 'date', 'required_with:items.*.batches.*.quantity'],
            'items.*.batches.*.quantity'                => ['required', 'numeric', 'min:0'],
            'items.*.batches.*.purchase_price'          => ['required', 'numeric', 'min:0'],
            'items.*.batches.*.sales_price'             => ['required', 'numeric', 'min:0'],
            'paid_amount'                               => ['nullable', 'numeric', 'min:0'],
            'payment_method'                            => ['required_with:paid_amount', 'in:cash,bank_transfer,cheque,mobile_banking'],
            'payment_reference_no'                      => ['nullable', 'string', 'max:100'],
            'payment_notes'                             => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Business-rule validation that requires DB lookups —
     * runs AFTER basic field validation passes.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $po = PurchaseOrder::with('items')->find($this->input('purchase_order_id'));

            if (! $po) {
                return;
            }

            if (! in_array($po->status, ['sent', 'partially_received'])) {
                $validator->errors()->add('purchase_order_id', 'This purchase order is not in a receivable state.');
                return;
            }

            foreach ($this->input('items', []) as $index => $itemPayload) {
                $poItem = $po->items->firstWhere('id', (int) ($itemPayload['purchase_order_item_id'] ?? null));

                if (! $poItem) {
                    $validator->errors()->add("items.$index.purchase_order_item_id", 'This item does not belong to the selected purchase order.');
                    continue;
                }

                $pending = $poItem->quantity_ordered - $poItem->quantity_received;

                $requestedQty = collect($itemPayload['batches'] ?? [])
                    ->sum(fn ($b) => (float) ($b['quantity'] ?? 0));

                if ($requestedQty <= 0) {
                    continue;
                }

                if ($requestedQty > $pending) {
                    $validator->errors()->add(
                        "items.$index.batches",
                        "Total quantity ({$requestedQty}) exceeds pending quantity ({$pending}) for {$poItem->product->name}."
                    );
                }

                // Every batch row with a quantity must have an expiry date
                foreach ($itemPayload['batches'] as $bIndex => $batch) {
                    $qty = (float) ($batch['quantity'] ?? 0);
                    if ($qty > 0 && empty($batch['expire_date'])) {
                        $validator->errors()->add("items.$index.batches.$bIndex.expire_date", 'Expiry date is required when a quantity is entered.');
                    }
                }
            }
        });
    }
}
