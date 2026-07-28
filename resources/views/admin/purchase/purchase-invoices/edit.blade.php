@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Invoices', 'link' => route('admin.purchase-invoices.manage')],
        ['name' => 'Edit PINV-0511', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Invoice — PINV-0511</h2>
            <x-status-badge status="due" />
        </div>

        <form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        <option selected>Square Pharmaceuticals</option>
                        <option>Renata Limited</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Invoice Date</label>
                    <x-form.date-picker
                        id="invoice_date"
                        name="invoice_date"
                        value="2026-07-10"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Due Date</label>
                    <x-form.date-picker
                        id="due_date"
                        name="due_date"
                        value="2026-07-24"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    />
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-center w-28">Qty</th>
                            <th class="px-4 py-3 text-right w-36">Unit Cost</th>
                            <th class="px-4 py-3 text-right w-36">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach([
                            ['name' => 'Napa Extra 500mg (100s)', 'qty' => 40, 'cost' => 850],
                            ['name' => 'Seclo 20mg (14s)', 'qty' => 22, 'cost' => 620],
                            ['name' => 'Fexo 120mg (10s)', 'qty' => 25, 'cost' => 480],
                        ] as $item)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                                <td class="px-4 py-2.5">
                                    <input type="number" value="{{ $item['qty'] }}" min="0"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center">
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="number" value="{{ $item['cost'] }}" min="0" step="0.01"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-right">
                                </td>
                                <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-200">৳ {{ number_format($item['qty'] * $item['cost']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.purchase-invoice.show', 1) }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Update Invoice
                </button>
            </div>
        </form>
    </div>

@endsection
