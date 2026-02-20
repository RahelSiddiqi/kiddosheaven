@props([
    'padding' => true,
    'shadow'  => true,
])

@php
$classes = 'bg-white rounded-xl border border-[var(--color-border)]'
    . ($shadow ? ' shadow-[var(--shadow-sm)]' : '')
    . ' overflow-hidden';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-[var(--color-border)] flex items-center justify-between">
            {{ $header }}
        </div>
    @endif

    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-[var(--color-border)] bg-[var(--color-muted)]">
            {{ $footer }}
        </div>
    @endif
</div>
