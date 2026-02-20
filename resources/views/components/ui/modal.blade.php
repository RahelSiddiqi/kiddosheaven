@props([
    'id'       => 'modal',
    'maxWidth' => 'lg',
    'title'    => null,
])

@php
$widths = [
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    'full'=> 'max-w-full',
];
$width = $widths[$maxWidth] ?? $widths['lg'];
@endphp

<div
    id="{{ $id }}"
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    class="fixed inset-0 z-[var(--z-modal)] flex items-center justify-center p-4"
    style="display:none"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        x-on:click="open = false"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative z-10 w-full {{ $width }} bg-white rounded-xl shadow-xl border border-[var(--color-border)] overflow-hidden"
    >
        @if($title || isset($actions))
            <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--color-border)]">
                @if($title)
                    <h3 class="text-base font-semibold text-[var(--color-foreground)]">{{ $title }}</h3>
                @endif
                <button type="button" x-on:click="open = false" class="ml-auto p-1 rounded-md hover:bg-[var(--color-muted)] transition-colors text-[var(--color-muted-foreground)]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="p-6">{{ $slot }}</div>

        @if(isset($footer))
            <div class="px-6 py-4 border-t border-[var(--color-border)] bg-[var(--color-muted)] flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
