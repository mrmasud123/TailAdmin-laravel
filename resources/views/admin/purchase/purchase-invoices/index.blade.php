@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Purchase Invoices', 'link' => '#']]" />

    @php
        $invoices = [
            ['no' => 'PINV-0512', 'supplier' => 'Square Pharmaceuticals', 'date' => '14 Jul 2026', 'due' => '28 Jul 2026', 'total' => '৳ 46,750', 'paid' => '৳ 46,750', 'due_amt' => '৳ 0', 'status' => 'paid'],
            ['no' => 'PINV-0511', 'supplier' => 'Square Pharmaceuticals', 'date' => '10 Jul 2026', 'due' => '24 Jul 2026', 'total' => '৳ 72,300', 'paid' => '৳ 40,000', 'due_amt' => '৳ 32,300', 'status' => 'due'],
            ['no' => 'PINV-0508', 'supplier' => 'Renata Limited', 'date' => '05 Jul 2026', 'due' => '19 Jul 2026', 'total' => '৳ 55,900', 'paid' => '৳ 0', 'due_amt' => '৳ 55,900', 'status' => 'due'],
            ['no' => 'PINV-0503', 'supplier' => 'Opsonin Pharma', 'date' => '29 Jun 2026', 'due' => '06 Jul 2026', 'total' => '৳ 21,150', 'paid' => '৳ 0', 'due_amt' => '৳ 21,150', 'status' => 'overdue'],
        ];
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Purchase Invoice List</h2>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All Suppliers</option>
                    <option>Square Pharmaceuticals</option>
                    <option>Renata Limited</option>
                    <option>Opsonin Pharma</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Status</label>
                <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option>All</option>
                    <option>Paid</option>
                    <option>Due</option>
                    <option>Overdue</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="text-xs text-gray-500 dark:text-gray-300">Invoice Date</label>
                <x-form.date-picker
                    id="invoice_date"
                    name="invoice_date"
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
                        <th class="px-4 py-3 text-left">Invoice No.</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Invoice Date</th>
                        <th class="px-4 py-3 text-left">Due Date</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Paid</th>
                        <th class="px-4 py-3 text-right">Due</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($invoices as $i)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $i['no'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $i['supplier'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $i['date'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $i['due'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $i['total'] }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">{{ $i['paid'] }}</td>
                            <td class="px-4 py-3 text-right {{ $i['due_amt'] === '৳ 0' ? 'text-gray-400' : 'text-red-600 dark:text-red-400 font-medium' }}">{{ $i['due_amt'] }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$i['status']" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.purchase-invoice.show', 1) }}" title="View"
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.purchase-invoice.edit', 1) }}" title="Edit"
                                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-500/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                                    </a>
                                    @if($i['due_amt'] !== '৳ 0')
                                        <button title="Record Payment"
                                           class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-500/10">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a1.5 1.5 0 100 3h3.75A2.25 2.25 0 0021 12zM21 12v5.25A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25V6.75A2.25 2.25 0 015.25 4.5h9M21 12V9.75" /></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
