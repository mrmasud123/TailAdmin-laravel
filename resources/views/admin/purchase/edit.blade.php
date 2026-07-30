@extends('layouts.app')

@section('vendor-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/assets/js/edit-purchase-order.js'])
@endsection

@section('content')

    @php
        $colors = [
            'draft' => 'bg-gray-100 text-gray-600',
            'sent' => 'bg-blue-100 text-blue-600',
            'partially_received' => 'bg-yellow-100 text-yellow-700',
            'received' => 'bg-green-100 text-green-700',
            'closed' => 'bg-gray-200 text-gray-700',
            'cancelled' => 'bg-red-100 text-red-600',
        ];
    @endphp
    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Orders', 'link' => route('admin.purchase-orders.manage')],
        ['name' => 'Edit', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Purchase Order</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        PO #{{ $purchaseOrder->po_number ?? $purchaseOrder->id }}
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1 rounded-full {{ $colors[$purchaseOrder->status] ?? 'bg-gray-100 text-gray-600' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
            {{ ucwords(str_replace('_', ' ', $purchaseOrder->status)) }}
        </span>
            </div>

            @if(in_array($purchaseOrder->status, ['sent', 'partially_received']))
                <a href="{{ route('admin.grn.create', $purchaseOrder->id) }}"
                   class="group relative inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                      bg-linear-to-r from-emerald-500 to-teal-600
                      hover:from-emerald-600 hover:to-teal-700
                      text-white text-sm font-medium shadow-sm hover:shadow-md
                      transition-all duration-200 ease-out
                      focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <i class="fas fa-truck-loading text-sm group-hover:animate-pulse"></i>
                    <span>Receive Goods</span>
                    @if($purchaseOrder->status === 'partially_received')
                        <span class="ml-1 text-[10px] bg-white/25 px-1.5 py-0.5 rounded-full font-semibold">
                Partial
            </span>
                    @endif
                </a>
            @endif
        </div>
        <form id="purchase-order-form"
              action="{{ route('admin.purchase-order.update', $purchaseOrder->id) }}"
              >
            @csrf
            @method('PUT')

            <input type="hidden" value="{{$purchaseOrder?->id ?? 0}}" name="purchase_order_id">
            <!-- Header fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm" name="supplier_id">
                        <option value="" disabled>Select Supplier</option>
                        @forelse($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @empty
                            <option value="" disabled selected>No suppliers found.</option>
                        @endforelse
                    </select>
                    <p class="text-xs text-red-500 mt-1 js-error" data-field="supplier_id"></p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Order Date</label>
                    <x-form.date-picker
                        id="order_date"
                        name="order_date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        required
                        placeholder="Select date"
                        defaultDate="{{ \Carbon\Carbon::parse(old('order_date', $purchaseOrder->order_date))->format('Y-m-d') }}"
                    />
                    <p class="text-xs text-red-500 mt-1 js-error" data-field="order_date"></p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Expected Delivery</label>
                    <x-form.date-picker
                        id="expected_delivery_date"
                        name="expected_delivery_date"
                        placeholder="Select date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        defaultDate="{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse(old('expected_delivery_date', $purchaseOrder->expected_delivery_date))->format('Y-m-d') : '' }}"
                    />
                    <p class="text-xs text-red-500 mt-1 js-error" data-field="expected_delivery_date"></p>
                </div>
            </div>

            <!-- Line items -->
            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Items</h3>
                <button type="button" id="add-row-btn"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Item
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center w-28">Quantity</th>
                        <th class="px-4 py-3 text-right w-36">Unit Cost</th>
                        <th class="px-4 py-3 text-right w-36">Line Total</th>
                        <th class="px-4 py-3 w-12"></th>
                    </tr>
                    </thead>
                    <tbody id="po-items-body" class="divide-y divide-gray-100 dark:divide-white/5">

                    @forelse($purchaseOrder->items as $index => $item)
                        <tr data-row="{{ $index }}">
                            <td class="px-4 py-2.5">
                                <select class="js-product-select w-full" name="items[{{ $index }}][product_id]" required>
                                    <option value="{{ $item->product->id }}" selected>{{ $item->product->name }}</option>
                                </select>
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" min="1" step="1" value="{{ $item->quantity_ordered }}"
                                       class="js-qty w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center"
                                       name="items[{{ $index }}][qty]" required>
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" min="0" step="0.01" value="{{ $item->unit_cost }}"
                                       class="js-cost w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-right"
                                       name="items[{{ $index }}][cost]" required>
                            </td>
                            <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-200 js-line-total">
                                {{ number_format($item->total_price, 2) }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <button type="button" class="js-remove-row text-red-500 hover:text-red-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">No items.</td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>
            <p class="text-xs text-red-500 mt-1 js-error" data-field="items"></p>

            <!-- Totals + notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Notes</label>
                        <textarea rows="4" name="notes" placeholder="Optional notes for the supplier..."
                                  class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        <p class="text-xs text-red-500 mt-1 js-error" data-field="notes"></p>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-300">Status</label>

                        @if(in_array($purchaseOrder->status, ['partially_received', 'received']))
                            {{-- System-managed statuses: driven by GRN completion, no manual override --}}
                            <div class="mt-1 flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-white/5">
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $colors[$purchaseOrder->status] ?? 'bg-gray-100 text-gray-600' }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                {{ ucwords(str_replace('_', ' ', $purchaseOrder->status)) }}
            </span>
                                <i class="fas fa-lock text-gray-400 text-xs ml-auto"></i>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-info-circle"></i>
                                Status is set automatically from goods receipts and can't be edited here.
                            </p>
                        @else
                            <select name="status" id="po-status"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                                @foreach([
                                    'draft' => 'Draft',
                                    'sent' => 'Sent',
                                    'closed' => 'Closed',
                                    'cancelled' => 'Cancelled',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $purchaseOrder->status) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Manually override the current status if needed.</p>
                        @endif

                        <p class="text-xs text-red-500 mt-1 js-error" data-field="status"></p>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-end">
                    <div class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>৳ <span id="po-subtotal">{{ number_format($purchaseOrder->sub_total, 2) }}</span></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-500 dark:text-gray-400">
                            <span>Discount</span>
                            <input type="number" name="discount_amount" min="0" step="0.01"
                                   value="{{ old('discount_amount', $purchaseOrder->discount_amount) }}"
                                   class="js-discount w-24 text-right px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        </div>
                        <div class="flex justify-between items-center text-gray-500 dark:text-gray-400">
                            <span>Tax</span>
                            <input type="number" name="tax_amount" min="0" step="0.01"
                                   value="{{ old('tax_amount', $purchaseOrder->tax_amount) }}"
                                   class="js-tax w-24 text-right px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        </div>
                        <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span>Grand Total</span>
                            <span>৳ <span id="po-grand-total">{{ number_format($purchaseOrder->total_amount, 2) }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.purchase-orders.manage') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button
                    type="submit"
                    name="action"
                    @if($purchaseOrder->status === 'received') disabled @endif
                    class="js-submit-btn px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow disabled:opacity-50 disabled:cursor-not-allowed">
                    Save &amp; Send to Supplier
                </button>
            </div>
        </form>
    </div>

@endsection
