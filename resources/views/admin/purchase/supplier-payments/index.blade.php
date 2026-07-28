@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Supplier Payments', 'link' => '#']]" />

    @php
        $payments = [
            ['no' => 'SPAY-1082', 'supplier' => 'Square Pharmaceuticals', 'invoice' => 'PINV-0511', 'date' => '10 Jul 2026', 'method' => 'Bank Transfer', 'amount' => '৳ 40,000'],
            ['no' => 'SPAY-1081', 'supplier' => 'Square Pharmaceuticals', 'invoice' => 'PINV-0512', 'date' => '14 Jul 2026', 'method' => 'Cheque', 'amount' => '৳ 46,750'],
            ['no' => 'SPAY-1078', 'supplier' => 'Beximco Pharma', 'invoice' => 'PINV-0498', 'date' => '02 Jul 2026', 'method' => 'Cash', 'amount' => '৳ 18,300'],
            ['no' => 'SPAY-1074', 'supplier' => 'ACI Limited', 'invoice' => 'PINV-0489', 'date' => '25 Jun 2026', 'method' => 'Bank Transfer', 'amount' => '৳ 33,150'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Paid (This Month)</p>
            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">৳ 1,38,200</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Outstanding Payables</p>
            <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">৳ 3,15,200</p>
        </div>
        <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Invoices Overdue</p>
            <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">3</p>
        </div>
    </div>

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Supplier Payment List</h2>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All Suppliers</option>
                    <option>Square Pharmaceuticals</option>
                    <option>Beximco Pharma</option>
                    <option>ACI Limited</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Payment Method</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All</option>
                    <option>Bank Transfer</option>
                    <option>Cash</option>
                    <option>Cheque</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Payment Date</label>
                <x-form.date-picker
                    id="payment_date"
                    name="payment_date"
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
                        <th class="px-4 py-3 text-left">Payment No.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Against Invoice</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Method</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($payments as $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $p['no'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <a href="{{ route('admin.supplier-payment.history', 1) }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $p['supplier'] }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $p['invoice'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $p['date'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $p['method'] }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-medium">{{ $p['amount'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <button title="Print Receipt"
                                   class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
