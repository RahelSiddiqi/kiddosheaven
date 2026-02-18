@props(['category'])

<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
    class="group relative bg-white rounded-2xl p-5 md:p-6 text-center border border-gray-100 hover:border-primary/30 hover:shadow-lg transition-all duration-300">
    <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-3 rounded-2xl bg-gray-50 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 group-hover:bg-primary/10 transition-all duration-300">
        {{ $category->icon ?? '🧸' }}
    </div>
    <h3 class="font-bold text-gray-900 text-sm group-hover:text-primary transition">{{ $category->name }}</h3>
    @if (isset($category->products_count))
        <p class="text-xs text-gray-400 mt-1">{{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}</p>
    @endif
</a>
