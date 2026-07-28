@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Returns', 'link' => route('admin.purchase-returns.manage')],
        ['name' => 'PR-0041', 'link' => '#'],
    ]" />

    @php
        $items = [
            ['batch' => 'NP-2607', 'name' => 'Napa Extra 500mg (100s)', 'qty' => 5, 'cost' => 850],
            ['batch' => 'SC-1188', 'name' => 'Seclo 20mg (14s)', 'qty' => 3, 'cost' => 620],
        ];
        $total = collect($items)->sum(fn($i) => $i['qty'] * $i['cost']);
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">PR-0041</h2>
                    <x-status-badge status="pending" />
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Against <a href="{{ route('admin.grn.show', 1) }}" class="text-blue-600 dark:text-blue-400 hover:underline">GRN-0230</a> · Square Pharmaceuticals</p>
            </div>
            <div class="flex gap-2">
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Approve
                </button>
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-500/40 dark:text-red-400 dark:hover:bg-red-500/10 text-xs font-medium">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Reject
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
            <div>
                <p class="text-xs text-gray-400">Return Date</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">12 Jul 2026</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Reason</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">Damaged in transit</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Requested By</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">Masud Rana</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Total Value</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">৳ {{ number_format($total) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Batch</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Unit Cost</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $item['batch'] }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $item['qty'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">৳ {{ number_format($item['cost']) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">৳ {{ number_format($item['qty'] * $item['cost']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-xl bg-gray-50 border border-gray-200 dark:bg-white/5 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
            <strong class="text-gray-800 dark:text-white">Notes:</strong> Two units of Napa Extra arrived with crushed packaging; photos shared with supplier's account manager.
        </div>
    </div>

@endsection
