@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Purchase Orders', 'link' => '#']]" />

    @php
        $orders = [
            ['no' => 'PO-2026-0142', 'supplier' => 'Square Pharmaceuticals', 'date' => '11 Jul 2026', 'delivery' => '18 Jul 2026', 'items' => 12, 'amount' => '৳ 84,500', 'status' => 'pending'],
            ['no' => 'PO-2026-0141', 'supplier' => 'Beximco Pharma', 'date' => '10 Jul 2026', 'delivery' => '17 Jul 2026', 'items' => 20, 'amount' => '৳ 1,22,000', 'status' => 'approved'],
            ['no' => 'PO-2026-0140', 'supplier' => 'ACI Limited', 'date' => '09 Jul 2026', 'delivery' => '14 Jul 2026', 'items' => 8, 'amount' => '৳ 46,750', 'status' => 'received'],
            ['no' => 'PO-2026-0139', 'supplier' => 'Incepta Pharmaceuticals', 'date' => '07 Jul 2026', 'delivery' => '13 Jul 2026', 'items' => 15, 'amount' => '৳ 63,900', 'status' => 'sent'],
            ['no' => 'PO-2026-0138', 'supplier' => 'Renata Limited', 'date' => '05 Jul 2026', 'delivery' => '10 Jul 2026', 'items' => 6, 'amount' => '৳ 29,150', 'status' => 'rejected'],
            ['no' => 'PO-2026-0137', 'supplier' => 'Opsonin Pharma', 'date' => '02 Jul 2026', 'delivery' => '08 Jul 2026', 'items' => 10, 'amount' => '৳ 51,300', 'status' => 'draft'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Purchase Order List
            </h2>
            <div class="flex items-end justify-end">
                <a href="{{ route('admin.purchase-order.pending') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg shadow transition me-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Pending Approvals
                </a>
                <a href="{{ route('admin.purchase-order.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Purchase Order
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All Suppliers</option>
                    <option>Square Pharmaceuticals</option>
                    <option>Beximco Pharma</option>
                    <option>ACI Limited</option>
                    <option>Incepta Pharmaceuticals</option>
                </select>
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Status</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All</option>
                    <option>Draft</option>
                    <option>Sent</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Received</option>
                    <option>Rejected</option>
                </select>
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Order Date</label>
                <x-form.date-picker
                    id="order_date"
                    name="order_date"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                              bg-white dark:bg-gray-800
                              text-gray-800 dark:text-gray-100"
                    placeholder="Date Picker"
                />
            </div>

            <div class="flex items-end ms-auto">
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">PO No.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Order Date</th>
                        <th class="px-4 py-3 text-left">Expected Delivery</th>
                        <th class="px-4 py-3 text-center">Items</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($orders as $o)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $o['no'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o['supplier'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $o['date'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $o['delivery'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $o['items'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $o['amount'] }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$o['status']" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.purchase-order.print', 1) }}" title="Print"
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.purchase-order.edit', 1) }}" title="Edit"
                                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-500/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                                    </a>
                                    <button title="Delete"
                                       class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-500/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-5 text-sm text-gray-500 dark:text-gray-400">
            <span>Showing 1 to {{ count($orders) }} of {{ count($orders) }} entries</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/5">Previous</button>
                <button class="px-3 py-1.5 rounded-lg bg-blue-600 text-white">1</button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/5">Next</button>
            </div>
        </div>
    </div>

@endsection
