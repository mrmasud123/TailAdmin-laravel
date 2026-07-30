@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[['name' => 'Purchase Dashboard', 'link' => '#']]" />

    @php
        $iconPaths = [
            'doc'    => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-1.519-3.75L12 17.25m0 0l-1.481-3.75M12 17.25V21m-7.5-3.75h15A2.25 2.25 0 0021.75 15V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25V15a2.25 2.25 0 002.25 2.25z',
            'inbox'  => 'M3.75 9.75h4.5l1.5 3h4.5l1.5-3h4.5M3.75 9.75l1.148-6.02A1.5 1.5 0 016.372 2.5h11.256a1.5 1.5 0 011.474 1.23l1.148 6.02M3.75 9.75v8.25A1.5 1.5 0 005.25 19.5h13.5a1.5 1.5 0 001.5-1.5V9.75',
            'wallet' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a1.5 1.5 0 100 3h3.75A2.25 2.25 0 0021 12zM21 12v5.25A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25V6.75A2.25 2.25 0 015.25 4.5h9M21 12V9.75',
            'reply'  => 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3',
        ];
        $colors = [
            'blue'    => 'bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
            'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400',
            'amber'   => 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
            'red'     => 'bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400',
        ];
    @endphp

        <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        @foreach($stats as $s)
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $s['label'] }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ $s['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $s['hint'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 {{ $colors[$s['color']] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPaths[$s['icon']] }}" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Recent purchase orders -->
        <div class="xl:col-span-2 bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Purchase Orders</h2>
                <a href="{{ route('admin.purchase-orders.manage') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    View all
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <span class="iconify text-gray-300 dark:text-gray-600" data-icon="lucide:inbox" style="font-size:32px"></span>
                    <p class="mt-3 text-sm text-gray-400 dark:text-gray-500">No purchase orders yet</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">PO No.</th>
                            <th class="px-4 py-3 text-left">Supplier</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($recentOrders as $o)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $o->po_number }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o->supplier?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $o->order_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">৳ {{ number_format($o->total_amount, 2) }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$o->status" /></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Quick actions + alerts -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.purchase-order.create') }}" class="flex flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200">New PO</span>
                    </a>
                    <a href="" class="flex flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200">Receive Goods</span>
                    </a>
                    <a href="{{ route('admin.purchase-return.create') }}" class="flex flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition">
                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200">New Return</span>
                    </a>
                    <a href="{{ route('admin.supplier-payments.manage') }}" class="flex flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/10 transition">
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a1.5 1.5 0 100 3h3.75A2.25 2.25 0 0021 12zM21 12v5.25A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25V6.75A2.25 2.25 0 015.25 4.5h9M21 12V9.75" /></svg>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200">Record Payment</span>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Needs Attention</h2>
                @php
                    $alerts = [];
                    if (($stats[0]['hint'] ?? null) && str_contains($stats[0]['hint'], 'awaiting')) {
                        $alerts[] = ['color' => 'bg-amber-500', 'text' => $stats[0]['hint']];
                    }
                    if (($stats[2]['hint'] ?? null) && str_contains($stats[2]['hint'], 'due')) {
                        $alerts[] = ['color' => 'bg-red-500', 'text' => $stats[2]['hint'] . ' for payment'];
                    }
                @endphp

                @if(empty($alerts))
                    <p class="text-sm text-gray-400 dark:text-gray-500">Nothing needs attention right now.</p>
                @else
                    <ul class="space-y-3 text-sm">
                        @foreach($alerts as $alert)
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 h-2 w-2 rounded-full {{ $alert['color'] }} shrink-0"></span>
                                <span class="text-gray-600 dark:text-gray-300">{{ $alert['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

@endsection
