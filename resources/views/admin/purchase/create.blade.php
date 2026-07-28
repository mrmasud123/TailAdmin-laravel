@extends('layouts.app')

@section('vendor-scripts')
    @vite(['resources/assets/js/purchase-orders.js'])
@endsection

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Orders', 'link' => route('admin.purchase-orders.manage')],
        ['name' => 'Create', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">New Purchase Order</h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300">Draft</span>
        </div>

        <form id="purchase-order-form">
            <!-- Header fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm" name="supplier_id">
                        <option value="" disabled selected>Select Supplier</option>
                        @forelse($suppliers as $supplier)
                            <option value="{{$supplier->id}}" {{old('supplier_id', $supplier->id ?? '')}}>{{$supplier->name}}</option>
                        @empty
                            <option value="" disabled selected>No suppliers found.</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Order Date</label>
                    <x-form.date-picker
                        id="order_date"
                        name="order_date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        required
                        placeholder="Select date"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Expected Delivery</label>
                    <x-form.date-picker
                        id="expected_delivery"
                        name="expected_delivery"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        placeholder="Select date"
                    />
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
                    <!-- rows injected by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Totals + notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Notes</label>
                    <textarea rows="4" name="notes" placeholder="Optional notes for the supplier..."
                              class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm"></textarea>
                </div>
                <div class="flex flex-col items-end justify-end">
                    <div class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>৳ <span id="po-subtotal">0</span></span>
                        </div>
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Estimated Tax (0%)</span>
                            <span>৳ 0</span>
                        </div>
                        <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span>Grand Total</span>
                            <span>৳ <span id="po-grand-total">0</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.purchase-orders.manage') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" class="px-4 py-2 text-sm font-medium rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="send" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Save &amp; Send to Supplier
                </button>
            </div>
        </form>
    </div>

@endsection
