@props([
    'variant' => 'info',
    'title'   => null,
    'dismissible' => false,
])

@php
$variants = [
    'info'    => ['bg-blue-50 border-blue-200 text-blue-800',   'border-blue-300'],
    'success' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'border-emerald-300'],
    'warning' => ['bg-amber-50 border-amber-200 text-amber-800', 'border-amber-300'],
    'error'   => ['bg-red-50 border-red-200 text-red-800',   'border-red-300'],
    'default' => ['bg-[var(--color-muted)] border-[var(--color-border)] text-[var(--color-foreground)]', 'border-[var(--color-border)]'],
];

$icons = [
    'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
    'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'default' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
];

[$bg, $border] = $variants[$variant] ?? $variants['default'];
$icon = $icons[$variant] ?? $icons['default'];
@endphp

<div role="alert" {{ $attributes->merge(['class' => "rounded-lg border p-4 $bg"]) }}>
    <div class="flex gap-3">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            {!! $icon !!}
        </svg>
        <div class="flex-1 min-w-0">
            @if($title)
                <p class="font-semibold text-sm mb-1">{{ $title }}</p>
            @endif
            <div class="text-sm">{{ $slot }}</div>
        </div>
        @if($dismissible)
            <button type="button" onclick="this.closest('[role=alert]').remove()" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>
</div>
