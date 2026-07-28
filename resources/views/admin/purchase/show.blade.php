@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Goods Receipts', 'link' => route('admin.grn.manage')],
        ['name' => 'GRN-0230', 'link' => '#'],
    ]" />

    @php
        $items = [
            ['name' => 'Napa Extra 500mg (100s)', 'ordered' => 40, 'received' => 40, 'batch' => 'NP-2607', 'expiry' => 'Dec 2027'],
            ['name' => 'Seclo 20mg (14s)', 'ordered' => 30, 'received' => 22, 'batch' => 'SC-1188', 'expiry' => 'Mar 2028'],
            ['name' => 'Fexo 120mg (10s)', 'ordered' => 25, 'received' => 25, 'batch' => 'FX-0972', 'expiry' => 'Jan 2028'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">GRN-0230</h2>
                    <x-status-badge status="partial" />
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Against Purchase Order <a href="{{ route('admin.purchase-order.edit', 1) }}" class="text-blue-600 dark:text-blue-400 hover:underline">PO-2026-0136</a> · Square Pharmaceuticals</p>
            </div>
            <a href="{{ route('admin.grn.edit', 1) }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-white/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                Edit
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
            <div>
                <p class="text-xs text-gray-400">Receipt Date</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">10 Jul 2026</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Challan / Invoice No.</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">SQ-INV-88144</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Received By</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">Masud Rana</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Total Value</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">৳ 72,300</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center">Ordered</th>
                        <th class="px-4 py-3 text-center">Received</th>
                        <th class="px-4 py-3 text-left">Batch No.</th>
                        <th class="px-4 py-3 text-left">Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $item['ordered'] }}</td>
                            <td class="px-4 py-3 text-center {{ $item['received'] < $item['ordered'] ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-gray-700 dark:text-gray-200' }}">
                                {{ $item['received'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $item['batch'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $item['expiry'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-xl bg-amber-50 border border-amber-200 dark:bg-amber-500/10 dark:border-amber-800/50 px-4 py-3 text-sm text-amber-800 dark:text-amber-300">
            <strong>Remarks:</strong> Seclo 20mg short-shipped by 8 units — supplier notified, balance expected in next dispatch.
        </div>
    </div>

@endsection
