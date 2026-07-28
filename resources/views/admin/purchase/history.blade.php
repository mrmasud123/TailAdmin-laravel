@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Supplier Payments', 'link' => route('admin.supplier-payments.manage')],
        ['name' => 'Square Pharmaceuticals', 'link' => '#'],
    ]" />

    @php
        $history = [
            ['no' => 'SPAY-1081', 'invoice' => 'PINV-0512', 'date' => '14 Jul 2026', 'method' => 'Cheque', 'amount' => '৳ 46,750'],
            ['no' => 'SPAY-1082', 'invoice' => 'PINV-0511', 'date' => '10 Jul 2026', 'method' => 'Bank Transfer', 'amount' => '৳ 40,000'],
            ['no' => 'SPAY-1065', 'invoice' => 'PINV-0470', 'date' => '18 Jun 2026', 'method' => 'Bank Transfer', 'amount' => '৳ 62,400'],
            ['no' => 'SPAY-1042', 'invoice' => 'PINV-0441', 'date' => '02 Jun 2026', 'method' => 'Cash', 'amount' => '৳ 28,900'],
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Supplier summary -->
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-lg">S</div>
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white">Square Pharmaceuticals</p>
                    <p class="text-xs text-gray-400">Supplier since Jan 2024</p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Total Purchased (YTD)</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-100">৳ 8,42,500</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Total Paid (YTD)</dt>
                    <dd class="font-medium text-emerald-600 dark:text-emerald-400">৳ 7,10,300</dd>
                </div>
                <div class="flex justify-between border-t border-gray-100 dark:border-white/5 pt-3">
                    <dt class="text-gray-500 dark:text-gray-400">Outstanding Balance</dt>
                    <dd class="font-semibold text-red-600 dark:text-red-400">৳ 1,32,200</dd>
                </div>
            </dl>
            <button class="w-full mt-5 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">
                Record New Payment
            </button>
        </div>

        <!-- Payment history table -->
        <div class="lg:col-span-2 bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-5">Payment History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Payment No.</th>
                            <th class="px-4 py-3 text-left">Against Invoice</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($history as $h)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $h['no'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $h['invoice'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $h['date'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $h['method'] }}</td>
                                <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-medium">{{ $h['amount'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
