<div class="space-y-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm">
        <ol class="flex items-center gap-2 text-gray-500 overflow-x-auto">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li><a href="{{ route('catalog') }}" wire:navigate class="hover:text-primary">Shop</a></li>
            @if ($product->category)
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
                <li><a href="{{ route('catalog', ['category' => $product->category->id]) }}" wire:navigate class="hover:text-primary">{{ $product->category->name }}</a></li>
            @endif
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li class="text-gray-800 font-medium truncate">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid md:grid-cols-2 gap-8">
        {{-- Product Gallery --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="aspect-[4/3] bg-gray-50">
                    @if ($selectedImage)
                        <img src="{{ asset($selectedImage) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-6xl">🧸</span>
                        </div>
                    @endif

                    {{-- Badges --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if ($product->is_featured)
                            <span class="px-3 py-1 rounded-full bg-yellow-400 text-white text-sm font-bold">★ Featured</span>
                        @endif
                        @if ($product->created_at && $product->created_at->diffInDays() < 14)
                            <span class="px-3 py-1 rounded-full bg-green-500 text-white text-sm font-bold">New</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Thumbnails --}}
            @php
                $productImages = is_array($product->images) ? $product->images : [];
            @endphp
            @if (count($productImages) > 1)
                <div class="flex gap-3 overflow-x-auto">
                    @foreach ($productImages as $img)
                        <button wire:click="selectImage('{{ $img }}')"
                            class="w-20 h-20 rounded-lg overflow-hidden border-2 transition {{ $selectedImage === $img ? 'border-primary' : 'border-transparent hover:border-gray-300' }}">
                            <img src="{{ asset($img) }}" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="space-y-6">
            @if ($product->category)
                <a href="{{ route('catalog', ['category' => $product->category->id]) }}" wire:navigate
                    class="inline-flex items-center gap-1 text-primary hover:text-primary-dark font-medium">
                    {{ $product->category->name }}
                </a>
            @endif

            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

            @if ($product->short_description)
                <p class="text-gray-600 text-lg">{{ $product->short_description }}</p>
            @endif

            {{-- Price --}}
            <div class="flex items-center gap-4">
                <span class="text-4xl font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
                @if ($product->compare_at_price)
                    <span class="text-xl text-gray-400 line-through">৳{{ number_format($product->compare_at_price, 2) }}</span>
                @endif
            </div>

            {{-- Stock Status --}}
            @if ($product->stock_quantity > 0)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-sm text-green-600 font-medium">
                        @if ($product->stock_quantity <= 5)
                            Only {{ $product->stock_quantity }} left in stock!
                        @else
                            In Stock
                        @endif
                    </span>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-sm text-red-600 font-medium">Out of Stock</span>
                </div>
            @endif

            {{-- Add to Cart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-4 mb-4">
                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                    <div class="flex items-center border border-gray-200 rounded-lg">
                        <button wire:click="decrementQuantity" class="w-12 h-12 flex items-center justify-center hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <input type="number" wire:model="quantity" min="1" max="99"
                            class="w-16 h-12 text-center border-x border-gray-200 focus:outline-none">
                        <button wire:click="incrementQuantity" class="w-12 h-12 flex items-center justify-center hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button wire:click="addToCart"
                    class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                </button>
            </div>

            {{-- Description --}}
            @if ($product->description)
                <div class="prose max-w-none">
                    <h3 class="font-bold text-gray-900 mb-3">Description</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if ($related->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($related as $item)
                    <x-product-card :product="$item" wire:key="related-{{ $item->id }}" />
                @endforeach
            </div>
        </section>
    @endif
</div>
