@props(['items' => []])

<nav class="mb-6 text-sm" aria-label="Breadcrumb">
    <ol class="flex items-center gap-1.5 text-gray-500 overflow-x-auto whitespace-nowrap pb-1">
        <li>
            <a href="{{ route('home') }}" class="hover:text-primary transition">Home</a>
        </li>
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if (!$loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-primary transition">{{ $item['label'] }}</a>
                @else
                    <span class="text-gray-800 font-medium truncate max-w-[200px]">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
