@extends('layouts.app')

@section('vendor-scripts')
    @vite(['resources/assets/js/purchase-order-list.js'])
@endsection

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Purchase Orders', 'link' => '#']]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Purchase Order List
            </h2>
            <div class="flex items-end justify-end">
                <a href="{{ route('admin.purchase-order.pending') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg shadow transition me-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Pending Approvals
                </a>
                <a href="{{ route('admin.purchase-order.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Purchase Order
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-end gap-3 mb-6">
            <div class="flex-1 min-w-45">
                <label class="text-xs text-gray-500 dark:text-gray-300">Supplier</label>
                <select id="filter-supplier" class="p-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-40">
                <label class="text-xs text-gray-500 dark:text-gray-300">Status</label>
                <select id="filter-status" class="p-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                    <option value="">All</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="partially_received">Partially Received</option>
                    <option value="received">Received</option>
                    <option value="closed">Closed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="flex-1 min-w-40">
                <label class="text-xs text-gray-500 dark:text-gray-300">Order Date</label>
                <x-form.date-picker
                    id="filter-order-date"
                    name="order_date"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                              bg-white dark:bg-gray-800
                              text-gray-800 dark:text-gray-100"
                    placeholder="Date Picker"
                />
            </div>

            <div class="flex items-end ms-auto">
                <button id="filter-reset" type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="po-table" class="min-w-full text-sm w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">PO No.</th>
                    <th class="px-4 py-3 text-left">Supplier</th>
                    <th class="px-4 py-3 text-left">Order Date</th>
                    <th class="px-4 py-3 text-left">Expected Delivery</th>
                    <th class="px-4 py-3 text-center">Items</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                <!-- rows rendered by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

@endsection
