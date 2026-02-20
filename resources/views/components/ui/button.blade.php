@props([
    'variant' => 'default',
    'size'    => 'default',
    'type'    => 'button',
    'href'    => null,
    'loading' => false,
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-ring)] select-none whitespace-nowrap touch-action-manipulation';

$variants = [
    'default'     => 'bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] shadow-xs',
    'secondary'   => 'border border-[var(--color-border)] bg-white text-[var(--color-foreground)] hover:bg-[var(--color-muted)]',
    'outline'     => 'border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent hover:bg-[var(--color-primary-light)]',
    'ghost'       => 'text-[var(--color-foreground)] hover:bg-[var(--color-muted)] bg-transparent',
    'destructive' => 'bg-[var(--color-destructive)] text-white hover:bg-red-600 shadow-xs',
    'success'     => 'bg-[var(--color-success)] text-white hover:bg-emerald-600 shadow-xs',
    'link'        => 'text-[var(--color-primary)] underline-offset-4 hover:underline bg-transparent p-0 h-auto',
];

$sizes = [
    'default' => 'h-10 px-4 py-2 text-sm',
    'sm'      => 'h-8 px-3 py-1 text-xs',
    'lg'      => 'h-12 px-6 py-3 text-base',
    'xl'      => 'h-14 px-8 py-4 text-lg',
    'icon'    => 'h-10 w-10 p-0',
    'icon-sm' => 'h-8 w-8 p-0',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
        @if($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
