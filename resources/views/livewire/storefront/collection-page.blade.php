<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Collections</h1>
        <p class="text-gray-500 mt-1">Browse our curated product collections.</p>
    </div>

    @if($collections->isEmpty())
        <p class="text-gray-400 text-center py-16">No collections available.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($collections as $collection)
            <a href="{{ route('collections.show', $collection->slug) }}" wire:navigate
               class="group block bg-white rounded-xl overflow-hidden shadow hover:shadow-md transition-shadow">
                @if($collection->image_path)
                    <img src="{{ asset($collection->image_path) }}" alt="{{ $collection->name }}"
                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <span class="text-4xl">🛍️</span>
                    </div>
                @endif
                <div class="p-4">
                    <h2 class="font-semibold text-lg">{{ $collection->name }}</h2>
                    @if($collection->description)
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $collection->description }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">{{ $collection->products_count }} products</p>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
