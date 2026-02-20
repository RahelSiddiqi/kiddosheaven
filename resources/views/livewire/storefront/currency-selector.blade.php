<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.outside="open = false"
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $selected }}</span>
        <svg class="w-3 h-3 text-gray-400 transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-transition
        class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
        @foreach ($currencies as $code => $info)
        <button wire:click="change('{{ $code }}')"
            class="w-full flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition {{ $selected === $code ? 'text-primary font-semibold' : 'text-gray-700' }}">
            <span>{{ $info['name'] }}</span>
            <span class="font-mono text-gray-400 text-xs">{{ $code }}</span>
        </button>
        @endforeach
    </div>
</div>
