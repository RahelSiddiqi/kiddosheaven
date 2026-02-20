@props(['category'])

<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
    class="group flex items-center gap-2.5 bg-white rounded-xl px-3 py-2.5 border border-gray-100 hover:border-primary/40 hover:shadow-md hover:bg-primary/[0.03] transition-all duration-200">
    <div class="w-9 h-9 shrink-0 rounded-xl bg-gray-50 flex items-center justify-center text-lg group-hover:scale-110 group-hover:bg-primary/10 transition-all duration-200">
        {{ $category->icon ?? '🧸' }}
    </div>
    <div class="min-w-0">
        <h3 class="font-semibold text-gray-800 text-xs leading-tight truncate group-hover:text-primary transition">{{ $category->name }}</h3>
        @if (isset($category->products_count))
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}</p>
        @endif
    </div>
</a>
