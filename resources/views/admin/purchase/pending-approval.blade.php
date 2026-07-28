@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Orders', 'link' => route('admin.purchase-orders.manage')],
        ['name' => 'Pending Approval', 'link' => '#'],
    ]" />

    @php
        $pending = [
            ['no' => 'PO-2026-0142', 'supplier' => 'Square Pharmaceuticals', 'requested_by' => 'Masud Rana', 'date' => '11 Jul 2026', 'amount' => '৳ 84,500'],
            ['no' => 'PO-2026-0136', 'supplier' => 'ACI Limited', 'requested_by' => 'Farida Yasmin', 'date' => '30 Jun 2026', 'amount' => '৳ 38,200'],
            ['no' => 'PO-2026-0134', 'supplier' => 'Renata Limited', 'requested_by' => 'Masud Rana', 'date' => '27 Jun 2026', 'amount' => '৳ 55,900'],
            ['no' => 'PO-2026-0131', 'supplier' => 'Opsonin Pharma', 'requested_by' => 'Farida Yasmin', 'date' => '22 Jun 2026', 'amount' => '৳ 21,150'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Purchase Orders Awaiting Approval
            </h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400 font-medium">
                {{ count($pending) }} pending
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">PO No.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Requested By</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($pending as $o)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                <a href="{{ route('admin.purchase-order.edit', 1) }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $o['no'] }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o['supplier'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $o['requested_by'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $o['date'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $o['amount'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        Approve
                                    </button>
                                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-500/40 dark:text-red-400 dark:hover:bg-red-500/10 text-xs font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
