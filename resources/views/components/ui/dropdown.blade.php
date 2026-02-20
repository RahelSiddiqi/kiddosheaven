@props([
    'align' => 'left',
    'width' => '48',
])

@php
$alignments = [
    'left'  => 'left-0',
    'right' => 'right-0',
];
$align = $alignments[$align] ?? $alignments['left'];
@endphp

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-[var(--z-dropdown)] mt-1 {{ $align }} w-{{ $width }} min-w-max bg-white rounded-xl shadow-[var(--shadow-lg)] border border-[var(--color-border)] py-1 overflow-hidden"
        style="display:none"
        x-on:click="open = false"
    >
        {{ $slot }}
    </div>
</div>
