@props(['type' => 'default', 'value' => null])

@php
    $classes = match($type) {
        'new' => 'bg-emerald-500 text-white',
        'sale' => 'bg-red-500 text-white',
        'featured' => 'bg-amber-400 text-white',
        'hot' => 'bg-orange-500 text-white',
        'soldout' => 'bg-gray-800 text-white',
        default => 'bg-primary text-white',
    };

    $label = match($type) {
        'new' => 'NEW',
        'sale' => $value ? "-{$value}%" : 'SALE',
        'featured' => 'FEATURED',
        'hot' => 'HOT',
        'soldout' => 'SOLD OUT',
        default => $value ?? '',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-block px-2 py-0.5 rounded text-[10px] md:text-xs font-bold tracking-wide {$classes}"]) }}>
    {{ $label }}
</span>
