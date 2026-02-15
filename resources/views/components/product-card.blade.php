@props(['product'])

<article class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
    <a href="{{ route('products.show', $product->slug) }}" wire:navigate.hover>
        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
            @php
                $img = $product->primary_image ?? ($product->images[0] ?? null);
            @endphp
            @if ($img)
                <img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-4xl">🧸</span>
                </div>
            @endif

            {{-- Badges --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1.5">
                @if ($product->is_featured)
                    <span class="px-2 py-0.5 rounded-full bg-yellow-400 text-white text-xs font-bold shadow">★</span>
                @endif
                @if ($product->created_at && $product->created_at->diffInDays() < 7)
                    <span class="px-2 py-0.5 rounded-full bg-green-500 text-white text-xs font-bold shadow">New</span>
                @endif
                @if ($product->discount_price && $product->discount_price > 0)
                    <span class="px-2 py-0.5 rounded-full bg-red-500 text-white text-xs font-bold shadow">Sale</span>
                @endif
            </div>
        </div>
    </a>

    <div class="p-3 md:p-4">
        @if ($product->category)
            <span class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-primary text-xs font-semibold mb-2">
                {{ $product->category->name }}
            </span>
        @endif

        <a href="{{ route('products.show', $product->slug) }}" wire:navigate
            class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2 group-hover:text-primary transition">
            {{ $product->name }}
        </a>

        <div class="flex items-center justify-between mt-2 md:mt-3">
            <div class="flex flex-col">
                @if ($product->discount_price && $product->discount_price > 0)
                    <span class="text-xs text-gray-400 line-through">৳{{ number_format($product->price, 2) }}</span>
                    <span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->discount_price, 2) }}</span>
                @else
                    <span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if ($product->review_count > 0)
                <div class="flex items-center gap-1 text-yellow-400 text-xs">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.544z" />
                    </svg>
                    <span class="text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
                </div>
            @endif
        </div>

        {{-- Quick Add Button (Mobile) --}}
        <button wire:click="$parent.addToCart('{{ $product->slug }}')"
            class="w-full mt-3 py-2.5 rounded-lg bg-primary text-white text-sm font-medium flex items-center justify-center gap-2 hover:bg-primary-dark transition md:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add to Cart
        </button>
    </div>
</article>
