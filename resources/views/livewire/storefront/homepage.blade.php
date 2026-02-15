<div class="space-y-12">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-primary/20 via-accent/10 to-transparent rounded-3xl p-8 md:p-12">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                Welcome to Kiddo's Heaven! 🧸
            </h1>
            <p class="text-lg text-gray-600 mb-6">
                Discover amazing toys, games, and educational products for your little ones
            </p>
            <a href="{{ route('catalog') }}" wire:navigate
                class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition shadow-lg">
                Shop Now
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 hidden md:block">
            <div class="text-9xl">🎮🧸🎨🎪</div>
        </div>
    </section>

    {{-- Flash Sale (if active) --}}
    @if ($flashSale && $flashSale->products->isNotEmpty())
        <section class="bg-gradient-to-r from-red-500 to-pink-500 rounded-3xl p-6 md:p-8 text-white">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold mb-2">⚡ {{ $flashSale->name }}</h2>
                    <p class="text-white/90">{{ $flashSale->description }}</p>
                </div>
                <div class="text-right hidden md:block">
                    <p class="text-sm text-white/80">Ends in</p>
                    <p class="text-2xl font-bold">{{ $flashSale->end_date->diffForHumans() }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($flashSale->products as $product)
                    <x-product-card :product="$product" wire:key="flash-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured by Category --}}
    @foreach ($featuredByCategory as $categoryName => $products)
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $categoryName }}</h2>
                <a href="{{ route('catalog', ['category' => $products->first()->category_id]) }}" wire:navigate
                    class="text-primary hover:text-primary-dark font-semibold flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach ($products as $product)
                    <x-product-card :product="$product" wire:key="cat-{{ $categoryName }}-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- New Arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">🆕 New Arrivals</h2>
                <a href="{{ route('catalog', ['sort' => 'newest']) }}" wire:navigate
                    class="text-primary hover:text-primary-dark font-semibold flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach ($newArrivals as $product)
                    <x-product-card :product="$product" wire:key="new-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Best Sellers --}}
    @if ($bestSellers->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">🔥 Best Sellers</h2>
                <a href="{{ route('catalog', ['sort' => 'popular']) }}" wire:navigate
                    class="text-primary hover:text-primary-dark font-semibold flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach ($bestSellers as $product)
                    <x-product-card :product="$product" wire:key="best-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- All Featured Products (Fallback) --}}
    @if ($allFeatured->isNotEmpty() && empty($featuredByCategory))
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">⭐ Featured Products</h2>
                <a href="{{ route('catalog', ['featured' => 1]) }}" wire:navigate
                    class="text-primary hover:text-primary-dark font-semibold flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach ($allFeatured as $product)
                    <x-product-card :product="$product" wire:key="featured-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Newsletter CTA --}}
    <section class="bg-primary rounded-3xl p-8 md:p-12 text-center text-white">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold mb-4">Stay Updated!</h2>
            <p class="text-white/90 mb-6">Subscribe to our newsletter for exclusive deals and new arrivals</p>
            <form wire:submit.prevent class="flex gap-2 max-w-md mx-auto">
                <input type="email" placeholder="Your email address" required
                    class="flex-1 px-4 py-3 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit"
                    class="px-6 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition">
                    Subscribe
                </button>
            </form>
        </div>
    </section>
</div>
