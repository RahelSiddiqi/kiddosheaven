@props(['variant' => 'default', 'size' => 'default'])

@php
$base = 'inline-flex items-center font-semibold rounded-full border';

$variants = [
    'default'     => 'bg-[var(--color-primary)] text-white border-transparent',
    'secondary'   => 'bg-[var(--color-muted)] text-[var(--color-muted-foreground)] border-transparent',
    'outline'     => 'bg-transparent text-[var(--color-foreground)] border-[var(--color-border)]',
    'destructive' => 'bg-red-50 text-red-700 border-red-200',
    'success'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'warning'     => 'bg-amber-50 text-amber-700 border-amber-200',
    'info'        => 'bg-blue-50 text-blue-700 border-blue-200',
    'pending'     => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'processing'  => 'bg-blue-50 text-blue-700 border-blue-200',
    'shipped'     => 'bg-purple-50 text-purple-700 border-purple-200',
    'delivered'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'cancelled'   => 'bg-red-50 text-red-600 border-red-200',
    'refunded'    => 'bg-gray-50 text-gray-600 border-gray-200',
];

$sizes = [
    'default' => 'text-xs px-2.5 py-0.5',
    'sm'      => 'text-xs px-2 py-px',
    'lg'      => 'text-sm px-3 py-1',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['secondary']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
