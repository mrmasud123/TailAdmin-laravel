@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Purchase Returns', 'link' => '#']]" />

    @php
        $returns = [
            ['no' => 'PR-0041', 'grn' => 'GRN-0230', 'supplier' => 'Square Pharmaceuticals', 'date' => '12 Jul 2026', 'reason' => 'Damaged in transit', 'amount' => '৳ 6,200', 'status' => 'pending'],
            ['no' => 'PR-0040', 'grn' => 'GRN-0227', 'supplier' => 'Beximco Pharma', 'date' => '28 Jun 2026', 'reason' => 'Wrong item shipped', 'amount' => '৳ 9,800', 'status' => 'approved'],
            ['no' => 'PR-0039', 'grn' => 'GRN-0221', 'supplier' => 'ACI Limited', 'date' => '18 Jun 2026', 'reason' => 'Near expiry on arrival', 'amount' => '৳ 4,150', 'status' => 'completed'],
            ['no' => 'PR-0038', 'grn' => 'GRN-0219', 'supplier' => 'Incepta Pharmaceuticals', 'date' => '10 Jun 2026', 'reason' => 'Quantity mismatch', 'amount' => '৳ 2,250', 'status' => 'rejected'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Purchase Return List</h2>
            <a href="{{ route('admin.purchase-return.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Return
            </a>
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
                <label class="text-xs text-gray-500 dark:text-gray-300">Status</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Completed</option>
                    <option>Rejected</option>
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
                        <th class="px-4 py-3 text-left">Return No.</th>
                        <th class="px-4 py-3 text-left">GRN Ref.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Reason</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($returns as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $r['no'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $r['grn'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $r['supplier'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $r['date'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $r['reason'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $r['amount'] }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$r['status']" /></td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.purchase-return.show', 1) }}" title="View"
                                   class="p-1.5 inline-block rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
