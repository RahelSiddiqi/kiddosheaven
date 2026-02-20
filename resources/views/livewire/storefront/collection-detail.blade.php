<div>
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li>›</li>
            <li><a href="{{ route('collections.index') }}" wire:navigate class="hover:text-primary">Collections</a></li>
            <li>›</li>
            <li class="text-gray-800 font-medium">{{ $collection->name }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        @if($collection->image_path)
            <img src="{{ asset($collection->image_path) }}" alt="{{ $collection->name }}"
                 class="w-full h-48 object-cover rounded-xl mb-4">
        @endif
        <h1 class="text-3xl font-bold">{{ $collection->name }}</h1>
        @if ($collection->description)
            <p class="text-gray-500 mt-2">{{ $collection->description }}</p>
        @endif
    </div>

    @if($products->isEmpty())
        <p class="text-gray-400 text-center py-16">No products in this collection yet.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
            <a href="{{ route('products.show', $product->slug) }}" wire:navigate
               class="group block bg-white rounded-xl overflow-hidden shadow hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="{{ $product->image_url ?? asset('images/placeholder.png') }}" alt="{{ $product->name }}"
                         class="w-full aspect-square object-cover group-hover:scale-105 transition-transform duration-300">
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded">Sale</span>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="text-sm font-medium line-clamp-2">{{ $product->name }}</h3>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-primary font-semibold">৳{{ number_format($product->price, 0) }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-gray-400 line-through text-xs">৳{{ number_format($product->compare_price, 0) }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</div>
