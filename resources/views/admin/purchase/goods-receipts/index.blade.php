@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Goods Receipts', 'link' => '#']]" />

    @php
        $grns = [
            ['no' => 'GRN-0231', 'po' => 'PO-2026-0140', 'supplier' => 'ACI Limited', 'date' => '14 Jul 2026', 'items' => 8, 'amount' => '৳ 46,750', 'status' => 'received'],
            ['no' => 'GRN-0230', 'po' => 'PO-2026-0136', 'supplier' => 'Square Pharmaceuticals', 'date' => '10 Jul 2026', 'items' => 18, 'amount' => '৳ 72,300', 'status' => 'partial'],
            ['no' => 'GRN-0229', 'po' => 'PO-2026-0134', 'supplier' => 'Renata Limited', 'date' => '05 Jul 2026', 'items' => 12, 'amount' => '৳ 55,900', 'status' => 'received'],
            ['no' => 'GRN-0228', 'po' => 'PO-2026-0131', 'supplier' => 'Opsonin Pharma', 'date' => '29 Jun 2026', 'items' => 6, 'amount' => '৳ 21,150', 'status' => 'received'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Goods Receipt Notes</h2>
            <a href="{{ route('admin.purchase-orders.manage') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Receive Against PO
            </a>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All Suppliers</option>
                    <option>Square Pharmaceuticals</option>
                    <option>ACI Limited</option>
                    <option>Renata Limited</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Receipt Date</label>
                <x-form.date-picker
                    id="receipt_date"
                    name="receipt_date"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    placeholder="Date Picker"
                />
            </div>
            <div class="flex items-end ms-auto">
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">Reset</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">GRN No.</th>
                        <th class="px-4 py-3 text-left">PO No.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Receipt Date</th>
                        <th class="px-4 py-3 text-center">Items</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($grns as $g)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $g['no'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $g['po'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $g['supplier'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $g['date'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $g['items'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $g['amount'] }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$g['status']" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.grn.show', 1) }}" title="View"
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.grn.edit', 1) }}" title="Edit"
                                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-500/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
