@props(['status'])

@php
    $map = [
        'draft'       => 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300',
        'pending'     => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'sent'        => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
        'approved'    => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        'rejected'    => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
        'received'    => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        'partial'     => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'completed'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        'paid'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        'due'         => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'overdue'     => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
        'cancelled'   => 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400',
        'expired'     => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
        'near_expiry' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'ok'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
    ];
    $key = strtolower(str_replace(' ', '_', $status));
    $classes = $map[$key] ?? 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300';
    $label = ucwords(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium $classes"]) }}>
    {{ $label }}
</span>
