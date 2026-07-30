@extends('layouts.app')

@section('vendor-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/assets/js/product-grn.js'])
@endsection

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Goods Receipts', 'link' => route('admin.grn.manage')],
        ['name' => 'Receive Goods', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Receive Goods</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Against Purchase Order
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $purchaseOrder->po_number }}</span>
                    · {{ $purchaseOrder->supplier->company_name }}
                </p>
            </div>
            <x-status-badge :status="$purchaseOrder->status" />
        </div>

        <form action="{{ route('admin.grn.store') }}" method="POST" id="grn-form">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Receipt Date</label>
                    <x-form.date-picker
                        id="received_date"
                        name="received_date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        required
                        placeholder="Select date"
                        defaultDate="{{ now()->format('Y-m-d') }}"
                    />
                    <p class="text-xs text-red-500 mt-1 js-error" data-field="received_date"></p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Challan / Invoice No.</label>
                    <input type="text" name="challan_no" placeholder="e.g. SQ-INV-88213"
                           class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <p class="text-xs text-red-500 mt-1 js-error" data-field="challan_no"></p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Received By</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly
                           class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Items Awaiting Receipt</h3>
                <span class="text-xs text-gray-400">Pending = Ordered − already received</span>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center w-24">Ordered</th>
                        <th class="px-4 py-3 text-center w-24">Pending</th>
                        <th class="px-4 py-3 text-center w-28">Receiving Now</th>
                        <th class="px-4 py-3 text-left w-36">Batch No.</th>
                        <th class="px-4 py-3 text-left w-40">Mfg. Date</th>
                        <th class="px-4 py-3 text-left w-40">Expiry Date</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($purchaseOrder->items as $index => $item)

                        @php
                            $pending = $item->quantity_ordered - $item->quantity_received;
                        @endphp

                        @continue($pending <= 0)
                        <tr data-po-item="{{ $item->id }}" data-pending="{{ $item->quantity_ordered - $item->quantity_received }}" class="js-item-group">
                            <td class="px-4 py-2.5" colspan="7">
                                <input type="hidden" name="items[{{ $item->id }}][purchase_order_item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $item->id }}][product_id]" value="{{ $item->product_id }}">

                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $item->product->name }}</span>
                                    <span class="text-xs text-gray-400">Ordered: {{ $item->quantity_ordered }} · Pending: {{ $item->quantity_ordered - $item->quantity_received }}</span>
                                </div>

                                <table class="w-full text-sm js-batch-table">
                                    <thead class="text-xs text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="text-left font-normal py-1">Batch No.</th>
                                        <th class="text-left font-normal py-1">Expiry Date</th>
                                        <th class="text-center font-normal py-1 w-24">Qty</th>
                                        <th class="text-right font-normal py-1 w-28">Purchase Price</th>
                                        <th class="text-right font-normal py-1 w-28">Sales Price</th>
                                        <th class="w-8"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="js-batch-rows">
                                    <tr class="js-batch-row">
                                        <td class="pr-2 py-1">
                                            <input type="text" placeholder="e.g. PCM-2601"
                                                   name="items[{{ $item->id }}][batches][0][batch_no]"
                                                   class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                        </td>
                                        <td class="pr-2 py-1">
                                            <x-form.date-picker
                                                :id="'exp_'.$item->id.'_0'"
                                                :name="'items['.$item->id.'][batches][0][expire_date]'"
                                                class="w-full px-2 py-1.5 border rounded-lg"
                                                placeholder="Select date" required
                                            />
                                        </td>
                                        <td class="pr-2 py-1">
                                            <input type="number" min="0" step="1"
                                                   value="{{ $item->quantity_ordered - $item->quantity_received }}"
                                                   name="items[{{ $item->id }}][batches][0][quantity]"
                                                   class="w-full px-2 py-1.5 border rounded-lg text-center">
                                        </td>
                                        <td class="pr-2 py-1">
                                            <input type="number" min="0" step="0.01" value="{{ $item->unit_cost }}"
                                                   name="items[{{ $item->id }}][batches][0][purchase_price]"
                                                   class="w-full px-2 py-1.5 border rounded-lg text-right">
                                        </td>
                                        <td class="pr-2 py-1">
                                            <input type="number" min="0" step="0.01"
                                                   name="items[{{ $item->id }}][batches][0][sales_price]"
                                                   class="w-full px-2 py-1.5 border rounded-lg text-right">
                                        </td>
                                        <td class="py-1 text-center">
                                            <button type="button" class="js-remove-batch text-red-400 hover:text-red-600 hidden">✕</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <button type="button" class="js-add-batch text-xs text-blue-600 hover:text-blue-700 mt-1">
                                    + Split into another batch
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-red-500 mt-1 js-error" data-field="items"></p>
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Record Payment (optional)</h3>
                <p class="text-xs text-gray-400 mb-3">Only applies if this receipt fully completes the purchase order — an invoice is generated automatically at that point.</p>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Amount Paid Now</label>
                        <input type="number" step="0.01" min="0" name="paid_amount" placeholder="0.00"
                               class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Payment Method</label>
                        <select name="payment_method" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Reference No.</label>
                        <input type="text" name="payment_reference_no" placeholder="e.g. TXN-88213"
                               class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Payment Note</label>
                        <input type="text" name="payment_notes" placeholder="Optional"
                               class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    </div>
                </div>
                <p class="text-xs text-red-500 mt-1 js-error" data-field="paid_amount"></p>
            </div>
            <div class="mt-6">
                <label class="text-xs text-gray-500 dark:text-gray-300">Remarks</label>
                <textarea rows="3" name="notes" placeholder="Any discrepancy or damage notes..."
                          class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.grn.manage') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <input type="hidden" name="action" value="completed">
                <button type="submit"
                        class="js-submit-btn px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Confirm Receipt
                </button>
            </div>
        </form>
    </div>

@endsection


