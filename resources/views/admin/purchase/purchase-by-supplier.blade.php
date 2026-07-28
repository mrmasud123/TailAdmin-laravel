@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Reports', 'link' => '#'],
        ['name' => 'Purchase by Supplier', 'link' => '#'],
    ]" />

    @php
        $suppliers = [
            ['name' => 'Square Pharmaceuticals', 'orders' => 24, 'total' => 842500, 'paid' => 710300, 'due' => 132200],
            ['name' => 'Beximco Pharma', 'orders' => 19, 'total' => 615400, 'paid' => 615400, 'due' => 0],
            ['name' => 'ACI Limited', 'orders' => 15, 'total' => 388200, 'paid' => 355050, 'due' => 33150],
            ['name' => 'Incepta Pharmaceuticals', 'orders' => 11, 'total' => 264900, 'paid' => 264900, 'due' => 0],
            ['name' => 'Renata Limited', 'orders' => 9, 'total' => 198750, 'paid' => 142850, 'due' => 55900],
            ['name' => 'Opsonin Pharma', 'orders' => 7, 'total' => 121150, 'paid' => 100000, 'due' => 21150],
        ];
        $maxTotal = collect($suppliers)->max('total');
        $grandTotal = collect($suppliers)->sum('total');
        $grandDue = collect($suppliers)->sum('due');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Purchases (YTD)</p>
            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">৳ {{ number_format($grandTotal) }}</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Outstanding Across Suppliers</p>
            <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">৳ {{ number_format($grandDue) }}</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Active Suppliers</p>
            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ count($suppliers) }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Purchase by Supplier</h2>
            <button class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-white/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 12m0 0l4.5-4.5M12 12V3" /></svg>
                Export
            </button>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">From Date</label>
                <x-form.date-picker id="from_date" name="from_date"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    placeholder="Select date" />
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">To Date</label>
                <x-form.date-picker id="to_date" name="to_date"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                    placeholder="Select date" />
            </div>
            <div class="flex items-end ms-auto">
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">Reset</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-center">Orders</th>
                        <th class="px-4 py-3 text-right">Total Purchased</th>
                        <th class="px-4 py-3 text-right">Paid</th>
                        <th class="px-4 py-3 text-right">Due</th>
                        <th class="px-4 py-3 text-left w-40">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($suppliers as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $s['name'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $s['orders'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">৳ {{ number_format($s['total']) }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">৳ {{ number_format($s['paid']) }}</td>
                            <td class="px-4 py-3 text-right {{ $s['due'] > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-400' }}">৳ {{ number_format($s['due']) }}</td>
                            <td class="px-4 py-3">
                                <div class="h-2 rounded-full bg-gray-100 dark:bg-white/10 overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: {{ round($s['total'] / $maxTotal * 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-white">
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ collect($suppliers)->sum('orders') }}</td>
                        <td class="px-4 py-3 text-right">৳ {{ number_format($grandTotal) }}</td>
                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">৳ {{ number_format(collect($suppliers)->sum('paid')) }}</td>
                        <td class="px-4 py-3 text-right text-red-600 dark:text-red-400">৳ {{ number_format($grandDue) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
