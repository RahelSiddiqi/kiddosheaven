@props([
    'tabs'    => [],
    'default' => null,
])

@php
$activeTab = $default ?? array_key_first($tabs);
@endphp

<div x-data="{ activeTab: '{{ $activeTab }}' }">
    {{-- Tab list --}}
    <div class="flex border-b border-[var(--color-border)] gap-1 overflow-x-auto scrollbar-hide">
        @foreach($tabs as $key => $label)
            <button
                type="button"
                x-on:click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}'
                    ? 'border-b-2 border-[var(--color-primary)] text-[var(--color-primary)] font-medium'
                    : 'text-[var(--color-muted-foreground)] hover:text-[var(--color-foreground)] hover:bg-[var(--color-muted)]'"
                class="px-4 py-2.5 text-sm whitespace-nowrap rounded-t-lg transition-colors -mb-px focus:outline-none"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab panels come from slot --}}
    <div class="pt-4">{{ $slot }}</div>
</div>
