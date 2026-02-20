@extends('layouts.app')

@section('title', 'Home — Kiddo\'s Heaven')

@section('content')
    {{-- Hero Banner --}}
    <section class="mb-12 md:mb-16 -mt-2">
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-[#D8E4E5] to-[#E8E4DF]">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-4 right-10 w-32 h-32 bg-white/40 rounded-full blur-2xl"></div>
                <div class="absolute bottom-8 left-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>
            </div>
            <div class="relative px-6 py-12 md:py-16 lg:py-20 flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12">
                <div class="max-w-xl text-center lg:text-left order-2 lg:order-1">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-primary/10 text-primary text-xs md:text-sm font-semibold mb-4">New Season Collection</span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">Spark Joy &<br>Imagination</h1>
                    <p class="text-gray-600 text-base md:text-lg mb-8 max-w-lg mx-auto lg:mx-0">Discover safe, educational, and fun toys for your little ones. Premium quality toys that inspire creativity.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20">
                            Shop Now
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ route('about') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl border-2 border-gray-300 text-gray-800 font-bold hover:bg-white/50 transition">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="relative w-48 h-48 md:w-64 md:h-64 lg:w-80 lg:h-80">
                        <div class="absolute inset-0 bg-white/60 rounded-full blur-2xl"></div>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='100' r='80' fill='%23FFE4E1'/%3E%3Ccircle cx='70' cy='85' r='25' fill='%23D4A574'/%3E%3Ccircle cx='70' cy='80' r='8' fill='%23333'/%3E%3Cellipse cx='70' cy='95' rx='5' ry='3' fill='%23FFB6C1'/%3E%3Ccircle cx='130' cy='85' r='25' fill='%23B8E0D2'/%3E%3Ccircle cx='130' cy='80' r='8' fill='%23333'/%3E%3Cellipse cx='130' cy='95' rx='5' ry='3' fill='%23FFB6C1'/%3E%3Cellipse cx='100' cy='110' rx='20' ry='15' fill='%23F5DEB3'/%3E%3Ccircle cx='100' cy='60' r='8' fill='%23018790'/%3E%3C/svg%3E" alt="Toys" class="relative w-full h-full object-contain">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Shop by Category --}}
    @if ($homeCategories->isNotEmpty())
        <section class="mb-10 md:mb-12">
            <x-shop.ui.section-heading title="Shop by Category" />
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 md:gap-3">
                @foreach ($homeCategories as $category)
                    <x-shop.category.card :category="$category" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Flash Sales --}}
    @if ($flashSales && $flashSales->products->isNotEmpty())
        <section class="mb-12 md:mb-16">
            <div class="bg-linear-to-r from-red-500 to-orange-400 rounded-2xl p-5 md:p-6 text-white mb-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl">⚡</div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold">{{ $flashSales->name }}</h2>
                            <p class="text-white/80 text-sm">{{ $flashSales->description }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 rounded-lg px-4 py-2 self-start md:self-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-bold text-sm">Ends {{ $flashSales->ends_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5">
                @foreach ($flashSales->products->take(8) as $product)
                    <x-shop.product.card :product="$product" badge="sale" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Promo Banners --}}
    <section class="mb-12 md:mb-16">
        <div class="grid md:grid-cols-2 gap-4 md:gap-6">
            <a href="{{ route('catalog', ['featured' => 1]) }}" class="group relative overflow-hidden rounded-2xl bg-linear-to-br from-primary/5 to-primary/10 p-6 md:p-8 border border-primary/10">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1/2 h-full opacity-30">
                    <div class="w-32 h-32 bg-primary/20 rounded-full blur-2xl absolute right-4 top-1/2 -translate-y-1/2"></div>
                </div>
                <div class="relative">
                    <span class="text-primary font-semibold text-sm">Featured Collection</span>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mt-1 mb-2">Best Sellers</h3>
                    <p class="text-gray-600 text-sm mb-4">Discover our most loved toys by parents</p>
                    <span class="inline-flex items-center gap-1 text-primary font-semibold text-sm group-hover:gap-2 transition-all">
                        Shop Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
            <a href="{{ route('catalog', ['sort' => 'newest']) }}" class="group relative overflow-hidden rounded-2xl bg-linear-to-br from-red-50 to-orange-50 p-6 md:p-8 border border-red-100">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1/2 h-full opacity-30">
                    <div class="w-32 h-32 bg-red-200 rounded-full blur-2xl absolute right-4 top-1/2 -translate-y-1/2"></div>
                </div>
                <div class="relative">
                    <span class="text-red-500 font-semibold text-sm">Just Arrived</span>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mt-1 mb-2">New Arrivals</h3>
                    <p class="text-gray-600 text-sm mb-4">Check out the latest additions to our collection</p>
                    <span class="inline-flex items-center gap-1 text-red-500 font-semibold text-sm group-hover:gap-2 transition-all">
                        Explore <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </section>

    {{-- New Arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section class="mb-12 md:mb-16">
            <x-shop.ui.section-heading title="New Arrivals" subtitle="Fresh toys just for you" view-all-url="{{ route('catalog', ['sort' => 'newest']) }}" />
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-5">
                @foreach ($newArrivals->take(10) as $product)
                    <x-shop.product.card :product="$product" badge="new" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Best Sellers --}}
    @if ($bestSellers->isNotEmpty())
        <section class="mb-12 md:mb-16">
            <x-shop.ui.section-heading title="Best Sellers" subtitle="Most loved by parents" view-all-url="{{ route('catalog', ['sort' => 'popular']) }}" />
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-5">
                @foreach ($bestSellers->take(10) as $product)
                    <x-shop.product.card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured by Category --}}
    @foreach ($featuredByCategory as $categoryName => $products)
        @if ($products->isNotEmpty())
            <section class="mb-12 md:mb-16">
                @php $cat = $homeCategories->firstWhere('name', $categoryName); @endphp
                <x-shop.ui.section-heading
                    :title="$categoryName"
                    subtitle="Handpicked for you"
                    :view-all-url="$cat ? route('catalog', ['category_id' => $cat->id]) : null" />
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-5">
                    @foreach ($products->take(5) as $product)
                        <x-shop.product.card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    {{-- Trust Badges --}}
    <section class="mb-8">
        <x-shop.ui.trust-badges layout="horizontal" />
    </section>
@endsection
