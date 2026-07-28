@extends('layouts.app')

@section('content')

    @php
        $items = [
            ['name' => 'Napa Extra 500mg (100s)', 'qty' => 40, 'cost' => 850],
            ['name' => 'Seclo 20mg (14s)', 'qty' => 30, 'cost' => 620],
            ['name' => 'Fexo 120mg (10s)', 'qty' => 25, 'cost' => 480],
        ];
        $total = collect($items)->sum(fn($i) => $i['qty'] * $i['cost']);
    @endphp

    <div class="max-w-3xl mx-auto">

        <div class="flex justify-end mb-4 print:hidden">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                Print
            </button>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-10 print:shadow-none print:rounded-none">

            <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="h-9 w-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">M</div>
                        <span class="font-bold text-gray-800 dark:text-white">MRPharmacy</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">House 12, Road 4, Dhanmondi, Dhaka</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">contact@mrpharmacy.example · +880 1XXX-XXXXXX</p>
                </div>
                <div class="text-right">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Purchase Order</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PO-2026-0142</p>
                    <x-status-badge status="pending" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Supplier</p>
                    <p class="font-medium text-gray-800 dark:text-white">Square Pharmaceuticals Ltd.</p>
                    <p class="text-gray-500 dark:text-gray-400">Mohakhali, Dhaka-1212</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 dark:text-gray-400">Order Date: <span class="text-gray-800 dark:text-gray-200">11 Jul 2026</span></p>
                    <p class="text-gray-500 dark:text-gray-400">Expected Delivery: <span class="text-gray-800 dark:text-gray-200">18 Jul 2026</span></p>
                </div>
            </div>

            <table class="min-w-full text-sm mb-8">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Unit Cost</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($items as $i => $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item['name'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $item['qty'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">৳ {{ number_format($item['cost']) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">৳ {{ number_format($item['qty'] * $item['cost']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mb-10">
                <div class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>৳ {{ number_format($total) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span>Grand Total</span>
                        <span>৳ {{ number_format($total) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 pt-10 text-sm">
                <div class="border-t border-gray-300 dark:border-gray-600 pt-2 text-center text-gray-500 dark:text-gray-400">
                    Authorized Signature
                </div>
                <div class="border-t border-gray-300 dark:border-gray-600 pt-2 text-center text-gray-500 dark:text-gray-400">
                    Supplier Acknowledgement
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            aside, header, .print\:hidden { display: none !important; }
            main { overflow: visible !important; }
        }
    </style>

@endsection
