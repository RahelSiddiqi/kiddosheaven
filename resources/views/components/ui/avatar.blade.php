@props([
    'src'     => null,
    'name'    => null,
    'size'    => 'default',
    'shape'   => 'circle',
])

@php
$sizes = [
    'xs'      => 'h-6 w-6 text-xs',
    'sm'      => 'h-8 w-8 text-xs',
    'default' => 'h-10 w-10 text-sm',
    'lg'      => 'h-12 w-12 text-base',
    'xl'      => 'h-16 w-16 text-lg',
];
$shapes = [
    'circle'  => 'rounded-full',
    'square'  => 'rounded-lg',
];
$initials = $name
    ? collect(explode(' ', trim($name)))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('')
    : '?';
$sizeClass  = $sizes[$size]  ?? $sizes['default'];
$shapeClass = $shapes[$shape] ?? $shapes['circle'];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center shrink-0 overflow-hidden $sizeClass $shapeClass"]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $name ?? 'Avatar' }}" class="w-full h-full object-cover">
    @else
        <div class="w-full h-full flex items-center justify-center bg-[var(--color-primary-light)] text-[var(--color-primary)] font-semibold">
            {{ $initials }}
        </div>
    @endif
</div>
