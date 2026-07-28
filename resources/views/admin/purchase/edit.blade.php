@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Orders', 'link' => route('admin.purchase-orders.manage')],
        ['name' => 'PO-2026-0142', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6"
         x-data="{
            rows: [
                { product: 'Napa Extra 500mg (100s)', qty: 40, cost: 850 },
                { product: 'Seclo 20mg (14s)', qty: 30, cost: 620 },
                { product: 'Fexo 120mg (10s)', qty: 25, cost: 480 },
            ],
            addRow() { this.rows.push({ product: '', qty: 1, cost: 0 }) },
            removeRow(i) { if (this.rows.length > 1) this.rows.splice(i, 1) },
            total() { return this.rows.reduce((sum, r) => sum + (r.qty * r.cost), 0).toLocaleString() }
         }">

        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Purchase Order — PO-2026-0142</h2>
                <x-status-badge status="pending" />
            </div>
        </div>

        <form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        <option selected>Square Pharmaceuticals</option>
                        <option>Beximco Pharma</option>
                        <option>ACI Limited</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Order Date</label>
                    <x-form.date-picker
                        id="order_date"
                        name="order_date"
                        value="2026-07-11"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Expected Delivery</label>
                    <x-form.date-picker
                        id="expected_delivery"
                        name="expected_delivery"
                        value="2026-07-18"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    />
                </div>
            </div>

            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Items</h3>
                <button type="button" @click="addRow()"
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
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <template x-for="(row, i) in rows" :key="i">
                            <tr>
                                <td class="px-4 py-2.5">
                                    <input type="text" x-model="row.product"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="number" min="1" x-model.number="row.qty"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center">
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="number" min="0" step="0.01" x-model.number="row.cost"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-right">
                                </td>
                                <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-200" x-text="(row.qty * row.cost).toLocaleString()"></td>
                                <td class="px-4 py-2.5 text-center">
                                    <button type="button" @click="removeRow(i)" class="text-red-500 hover:text-red-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6">
                <div class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span>Grand Total</span>
                        <span x-text="'৳ ' + total()"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.purchase-orders.manage') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Update Purchase Order
                </button>
            </div>
        </form>
    </div>

@endsection
