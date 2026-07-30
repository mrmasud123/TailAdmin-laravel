@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Goods Receipts', 'link' => route('admin.grn.manage')],
        ['name' => $grn->grn_number, 'link' => '#'],
    ]" />

    @php
        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
            'completed' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400',
        ];
        $po = $grn->purchaseOrder;
        $totalValue = $grn->items->sum('subtotal');

        // Group this GRN's lines by the PO item they belong to, so batches of the
        // same product collapse into one progress row instead of duplicating it.
        $poItemProgress = $grn->items
            ->groupBy('purchase_order_item_id')
            ->map(function ($lines) {
                $poItem = $lines->first()->purchaseOrderItem;
                return [
                    'product' => $lines->first()->product?->name ?? 'N/A',
                    'ordered' => (float) ($poItem->quantity_ordered ?? 0),
                    'received_total' => (float) ($poItem->quantity_received ?? 0), // cumulative across ALL GRNs
                    'received_this_grn' => $lines->sum('quantity_received'),        // just this receipt
                ];
            });
    @endphp

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6 mb-6">

        <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $grn->grn_number }}</h2>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$grn->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($grn->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Against
                    @if($po)
                        <a href="{{ route('admin.purchase-order.edit', $po->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $po->po_number }}</a>
                    @else
                        <span class="text-gray-400">N/A</span>
                    @endif
                    · {{ $grn->supplier?->company_name ?? $grn->supplier?->name ?? 'N/A' }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.grn.edit', $grn->id) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 text-xs font-medium">
                    <span class="iconify" data-icon="lucide:edit" style="font-size:14px"></span>
                    Edit
                </a>
                <a href="{{ route('admin.purchase-return.create', ['grn' => $grn->id]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-medium">
                    <span class="iconify" data-icon="lucide:corner-up-left" style="font-size:14px"></span>
                    Raise Return
                </a>
            </div>
        </div>

        <!-- Info grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
            <div>
                <p class="text-xs text-gray-400">Received Date</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">{{ \Carbon\Carbon::parse($grn->received_date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Supplier</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">{{ $grn->supplier?->company_name ?? $grn->supplier?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Received By</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">{{ $grn->receiver?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Total Value (this receipt)</p>
                <p class="text-gray-800 dark:text-gray-100 font-medium">৳ {{ number_format($totalValue, 2) }}</p>
            </div>
        </div>

        <!-- PO receiving progress: since a PO can be received across multiple GRNs -->
        @if($poItemProgress->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Purchase Order Receiving Progress</h3>
                <div class="space-y-3">
                    @foreach($poItemProgress as $progress)
                        @php
                            $pct = $progress['ordered'] > 0 ? min(100, round(($progress['received_total'] / $progress['ordered']) * 100)) : 0;
                            $isComplete = $progress['received_total'] >= $progress['ordered'];
                        @endphp
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <div class="flex items-center justify-between mb-1.5 text-sm">
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $progress['product'] }}</span>
                                <span class="text-xs {{ $isComplete ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} font-medium">
                                    {{ (int) $progress['received_total'] }} / {{ (int) $progress['ordered'] }} received
                                    @if(!$isComplete)
                                        · {{ (int) ($progress['ordered'] - $progress['received_total']) }} remaining
                                    @endif
                                </span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full {{ $isComplete ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                                {{ (int) $progress['received_this_grn'] }} of these units received via this GRN
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- This GRN's batch-level lines -->
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Batch</th>
                    <th class="px-4 py-3 text-left">Mfg. Date</th>
                    <th class="px-4 py-3 text-left">Expiry</th>
                    <th class="px-4 py-3 text-center">Qty Received</th>
                    <th class="px-4 py-3 text-right">Unit Cost</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($grn->items as $item)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item->product?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $item->batch_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $item->manufacture_date ? $item->manufacture_date->format('d M Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            @if($item->expiry_date)
                                {{ $item->expiry_date->format('d M Y') }}
                                @if($item->expiry_date->isFuture() && $item->expiry_date->diffInDays(now()) <= 30)
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400">Near expiry</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-100">
                            {{ (int) $item->quantity_received }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">৳ {{ number_format($item->unit_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">৳ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">Total (this receipt)</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">৳ {{ number_format($totalValue, 2) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Financial summary (from linked PO — reflects full PO, not just this GRN) -->
        @if($po)
            <div class="mb-2">
                <p class="text-xs text-gray-400 mb-2">Purchase Order Total (full PO, may span multiple GRNs)</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700 px-4 py-3">
                    <p class="text-xs text-gray-400">Sub Total</p>
                    <p class="text-gray-800 dark:text-gray-100 font-semibold">৳ {{ number_format($po->sub_total, 2) }}</p>
                </div>
                <div class="rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 px-4 py-3">
                    <p class="text-xs text-rose-500 dark:text-rose-400">Discount</p>
                    <p class="text-rose-600 dark:text-rose-400 font-semibold">− ৳ {{ number_format($po->discount_amount, 2) }}</p>
                </div>
                <div class="rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 px-4 py-3">
                    <p class="text-xs text-amber-600 dark:text-amber-400">Tax</p>
                    <p class="text-amber-600 dark:text-amber-400 font-semibold">৳ {{ number_format($po->tax_amount, 2) }}</p>
                </div>
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-4 py-3">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Total Amount</p>
                    <p class="text-emerald-700 dark:text-emerald-400 font-bold">৳ {{ number_format($po->total_amount, 2) }}</p>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($grn->notes)
            <div class="rounded-xl bg-gray-50 border border-gray-200 dark:bg-white/5 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 mb-6">
                <strong class="text-gray-800 dark:text-white">Notes:</strong> {{ $grn->notes }}
            </div>
        @endif

        <!-- Linked purchase returns -->
        @if($grn->purchaseReturns->isNotEmpty())
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Related Purchase Returns</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Return No.</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Reason</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($grn->purchaseReturns as $return)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.purchase-return.show', $return->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        {{ $return->return_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $return->return_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ Str::limit($return->reason, 40) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">৳ {{ number_format($return->total_amount, 2) }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$return->status" /></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

@endsection
