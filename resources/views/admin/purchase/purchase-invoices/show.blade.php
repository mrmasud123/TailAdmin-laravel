@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Invoices', 'link' => route('admin.purchase-invoices.manage')],
        ['name' => 'PINV-0511', 'link' => '#'],
    ]" />

    @php
        $items = [
            ['name' => 'Napa Extra 500mg (100s)', 'qty' => 40, 'cost' => 850],
            ['name' => 'Seclo 20mg (14s)', 'qty' => 22, 'cost' => 620],
            ['name' => 'Fexo 120mg (10s)', 'qty' => 25, 'cost' => 480],
        ];
        $subtotal = collect($items)->sum(fn($i) => $i['qty'] * $i['cost']);

        $payments = [
            ['date' => '10 Jul 2026', 'method' => 'Bank Transfer', 'ref' => 'TXN-88213', 'amount' => '৳ 40,000'],
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">PINV-0511</h2>
                        <x-status-badge status="due" />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Square Pharmaceuticals · GRN-0230</p>
                </div>
                <a href="{{ route('admin.purchase-invoice.edit', 1) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-white/5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    Edit
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Invoice Date</p>
                    <p class="text-gray-800 dark:text-gray-100 font-medium">10 Jul 2026</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Due Date</p>
                    <p class="text-gray-800 dark:text-gray-100 font-medium">24 Jul 2026</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Payment Terms</p>
                    <p class="text-gray-800 dark:text-gray-100 font-medium">Net 14</p>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Cost</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($items as $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $item['qty'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">৳ {{ number_format($item['cost']) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">৳ {{ number_format($item['qty'] * $item['cost']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <div class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Subtotal</span><span>৳ {{ number_format($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Paid</span><span class="text-emerald-600 dark:text-emerald-400">৳ 40,000</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span>Balance Due</span><span class="text-red-600 dark:text-red-400">৳ {{ number_format($subtotal - 40000) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Payment History</h3>
                <ul class="space-y-4">
                    @foreach($payments as $p)
                        <li class="flex items-start justify-between text-sm border-b border-gray-100 dark:border-white/5 pb-3 last:border-0 last:pb-0">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $p['method'] }}</p>
                                <p class="text-xs text-gray-400">{{ $p['date'] }} · Ref: {{ $p['ref'] }}</p>
                            </div>
                            <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $p['amount'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <button class="w-full mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">
                    Record New Payment
                </button>
            </div>
        </div>
    </div>

@endsection
