@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Reports', 'link' => '#'],
        ['name' => 'Expiry Report', 'link' => '#'],
    ]" />

    @php
        $batches = [
            ['product' => 'Seclo 20mg (14s)', 'batch' => 'SC-1102', 'qty' => 18, 'expiry' => '25 Jul 2026', 'days' => 12, 'status' => 'near_expiry'],
            ['product' => 'Amodis 500mg (10s)', 'batch' => 'AM-0876', 'qty' => 34, 'expiry' => '02 Aug 2026', 'days' => 20, 'status' => 'near_expiry'],
            ['product' => 'Napa Extra 500mg (100s)', 'batch' => 'NP-2214', 'qty' => 6, 'expiry' => '05 Jun 2026', 'days' => -38, 'status' => 'expired'],
            ['product' => 'Fexo 120mg (10s)', 'batch' => 'FX-0812', 'qty' => 12, 'expiry' => '18 Jun 2026', 'days' => -25, 'status' => 'expired'],
            ['product' => 'Ace Plus 500mg (10s)', 'batch' => 'AP-1330', 'qty' => 50, 'expiry' => '30 Dec 2026', 'days' => 170, 'status' => 'ok'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Already Expired</p>
            <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">2 batches</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Expiring Within 30 Days</p>
            <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">2 batches</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Units at Risk</p>
            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">70 units</p>
        </div>
    </div>

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Expiry Report</h2>
            <button class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-white/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 12m0 0l4.5-4.5M12 12V3" /></svg>
                Export
            </button>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Expiry Within</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All</option>
                    <option>Already Expired</option>
                    <option>30 Days</option>
                    <option>60 Days</option>
                    <option>90 Days</option>
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Product / Category</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All Products</option>
                    <option>Analgesics</option>
                    <option>Antibiotics</option>
                    <option>Antihistamines</option>
                </select>
            </div>
            <div class="flex items-end ms-auto">
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">Reset</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">Batch No.</th>
                        <th class="px-4 py-3 text-center">Qty in Stock</th>
                        <th class="px-4 py-3 text-left">Expiry Date</th>
                        <th class="px-4 py-3 text-center">Days Left</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($batches as $b)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $b['product'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $b['batch'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $b['qty'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $b['expiry'] }}</td>
                            <td class="px-4 py-3 text-center {{ $b['days'] < 0 ? 'text-red-600 dark:text-red-400 font-medium' : ($b['days'] <= 30 ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-gray-600 dark:text-gray-300') }}">
                                {{ $b['days'] < 0 ? abs($b['days']) . ' days ago' : $b['days'] . ' days' }}
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$b['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
