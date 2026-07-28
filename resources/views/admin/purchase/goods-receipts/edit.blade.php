@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Goods Receipts', 'link' => route('admin.grn.manage')],
        ['name' => 'Edit GRN-0230', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit GRN-0230</h2>
            <x-status-badge status="partial" />
        </div>

        <form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Receipt Date</label>
                    <x-form.date-picker
                        id="receipt_date"
                        name="receipt_date"
                        value="2026-07-10"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Challan / Invoice No.</label>
                    <input type="text" value="SQ-INV-88144"
                           class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Received By</label>
                    <input type="text" value="Masud Rana" readonly
                           class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-sm">
                </div>
            </div>

            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Items</h3>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-center">Ordered</th>
                            <th class="px-4 py-3 text-center">Received</th>
                            <th class="px-4 py-3 text-left">Batch No.</th>
                            <th class="px-4 py-3 text-left">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach([
                            ['name' => 'Napa Extra 500mg (100s)', 'ordered' => 40, 'received' => 40, 'batch' => 'NP-2607', 'expiry' => '2027-12-01'],
                            ['name' => 'Seclo 20mg (14s)', 'ordered' => 30, 'received' => 22, 'batch' => 'SC-1188', 'expiry' => '2028-03-01'],
                            ['name' => 'Fexo 120mg (10s)', 'ordered' => 25, 'received' => 25, 'batch' => 'FX-0972', 'expiry' => '2028-01-01'],
                        ] as $item)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                                <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400">{{ $item['ordered'] }}</td>
                                <td class="px-4 py-2.5">
                                    <input type="number" value="{{ $item['received'] }}" min="0"
                                           class="w-24 mx-auto block px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center">
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="text" value="{{ $item['batch'] }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                                </td>
                                <td class="px-4 py-2.5">
                                    <x-form.date-picker
                                        :id="'expiry_'.Str::slug($item['name'])"
                                        :name="'expiry_'.Str::slug($item['name'])"
                                        :value="$item['expiry']"
                                        class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <label class="text-xs text-gray-500 dark:text-gray-300">Remarks</label>
                <textarea rows="3"
                          class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">Seclo 20mg short-shipped by 8 units — supplier notified, balance expected in next dispatch.</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.grn.show', 1) }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Update Receipt
                </button>
            </div>
        </form>
    </div>

@endsection
