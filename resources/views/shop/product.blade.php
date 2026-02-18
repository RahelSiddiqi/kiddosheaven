@extends('layouts.app')

@section('title', $product->name . ' — Kiddo\'s Heaven')

@section('content')
    {{-- Breadcrumb --}}
    <x-shop.layout.breadcrumb :items="array_filter([
        ['label' => 'Shop', 'url' => route('catalog')],
        $product->category ? ['label' => $product->category->name, 'url' => route('catalog', ['category_id' => $product->category->id])] : null,
        ['label' => $product->name, 'url' => '#'],
    ])" />

    {{-- ===== Product Top Section ===== --}}
    <div class="grid md:grid-cols-2 gap-6 md:gap-10 mb-10">
        {{-- Gallery --}}
        <x-shop.product.gallery :product="$product" />

        {{-- Product Info --}}
        <div class="space-y-5">
            {{-- Category & Brand --}}
            <div class="flex items-center gap-2 flex-wrap">
                @if ($product->brand)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-100 px-2.5 py-1 rounded-full">
                        @if ($product->brand->logo)
                            <img src="{{ asset('storage/' . $product->brand->logo) }}" alt="{{ $product->brand->name }}" class="w-4 h-4 object-contain rounded">
                        @endif
                        {{ $product->brand->name }}
                    </span>
                @endif
                @if ($product->category)
                    <a href="{{ route('catalog', ['category_id' => $product->category->id]) }}"
                        class="inline-flex items-center text-xs font-medium text-primary hover:text-primary-dark transition bg-primary/5 px-2.5 py-1 rounded-full">
                        {{ $product->category->name }}
                    </a>
                @endif
                @if ($product->product_type === 'digital')
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Digital
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

            {{-- Rating & SKU --}}
            <div class="flex items-center gap-4 flex-wrap">
                <x-shop.product.rating-stars :rating="$product->average_rating" :count="$product->review_count" size="md" />
                @if ($product->sku)
                    <span class="text-xs text-gray-400">SKU: {{ $product->sku }}</span>
                @endif
            </div>

            {{-- Short Description --}}
            @if ($product->short_description)
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">{!! $product->short_description !!}</div>
            @endif

            {{-- Price (for simple products) --}}
            @if ($product->product_type !== 'variable')
                <x-shop.product.price :product="$product" size="xl" />
            @endif

            {{-- Stock (for simple products) --}}
            @if ($product->product_type !== 'variable')
                <x-shop.product.stock-badge :quantity="$product->stock_quantity" />
            @endif

            {{-- Variant Selector (for variable products) - includes dynamic price & stock --}}
            <x-shop.product.variant-selector :product="$product" :variants="$variants" />

            {{-- Certifications --}}
            @if ($product->halal_certified || $product->organic_certified)
                <div class="flex flex-wrap gap-2">
                    @if ($product->halal_certified)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Halal Certified
                        </span>
                    @endif
                    @if ($product->organic_certified)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            Organic Certified
                        </span>
                    @endif
                </div>
            @endif

            {{-- Add to Cart --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <form action="{{ route('cart.add', $product->slug) }}" method="post" id="addToCartForm">
                    @csrf
                    <input type="hidden" name="variant_id" value="{{ $product->product_type === 'variable' && $variants->isNotEmpty() ? ($variants->firstWhere('is_default', true)?->id ?? $variants->first()->id) : '' }}">

                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Quantity --}}
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700">Qty:</label>
                            <div class="flex items-center border border-gray-200 rounded-lg" x-data="{ qty: 1 }">
                                <button type="button" @click="qty = Math.max(1, qty - 1); $refs.qtyInput.value = qty"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-l-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" name="quantity" x-ref="qtyInput" x-model="qty" min="1" max="99"
                                    class="w-14 h-10 text-center border-x border-gray-200 focus:outline-none text-sm font-medium">
                                <button type="button" @click="qty = Math.min(99, qty + 1); $refs.qtyInput.value = qty"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-r-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Add to Cart Button --}}
                        <button type="submit"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/25 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $product->stock_quantity <= 0 && $product->product_type !== 'variable' ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Add to Cart
                        </button>
                    </div>
                </form>

                {{-- Add to Wishlist Button --}}
                @auth
                    <button type="button"
                        x-data="{
                            inWishlist: {{ $inWishlist ? 'true' : 'false' }},
                            loading: false,
                            async toggle() {
                                if (this.loading) return;
                                this.loading = true;
                                try {
                                    const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.inWishlist = data.in_wishlist;
                                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: data.message } }));
                                    }
                                } catch(e) {
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Something went wrong' } }));
                                } finally { this.loading = false; }
                            }
                        }"
                        @click="toggle()"
                        :disabled="loading"
                        :class="inWishlist ? 'border-red-400 text-red-500 hover:border-red-500' : 'border-gray-200 text-gray-700 hover:border-primary hover:text-primary'"
                        class="mt-3 w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border-2 font-semibold transition active:scale-[0.98]">
                        <svg class="w-5 h-5 transition" :class="inWishlist ? 'fill-current text-red-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span x-text="inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'"></span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="mt-3 w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border-2 border-gray-200 text-gray-700 font-semibold hover:border-primary hover:text-primary transition active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Add to Wishlist
                    </a>
                @endauth

                {{-- Quick Trust Points --}}
                @php
                    $threshold = $siteSettings['free_shipping_threshold'] ?? 0;
                    $currency  = $siteSettings['currency'] ?? 'BDT';
                    $currSym   = $currency === 'BDT' ? '৳' : ($currency === 'USD' ? '$' : $currency);
                    $codOn     = ($siteSettings['cod_enabled'] ?? '1') == '1';
                    $safeOn    = ($siteSettings['safe_non_toxic'] ?? '1') == '1';
                    $retDays   = $siteSettings['return_policy_days'] ?? '30';
                @endphp
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                    @if ($threshold > 0)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Free Shipping {{ $currSym }}{{ number_format($threshold, 0) }}+</span>
                    </div>
                    @endif
                    @if ($codOn)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Cash on Delivery</span>
                    </div>
                    @endif
                    @if ($safeOn)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Safe & Non-Toxic</span>
                    </div>
                    @endif
                    @if ($retDays > 0)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>{{ $retDays }}-Day Returns</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tabbed Content Section ===== --}}
    @php
        $hasFeatures        = trim(strip_tags($product->features ?? '')) !== '';
        $hasCare            = trim(strip_tags($product->care_instructions ?? '')) !== '';
        $hasIngredients     = trim(strip_tags($product->ingredients ?? '')) !== '';
        $hasSafety          = trim(strip_tags($product->safety_warning ?? '')) !== '';
        $hasManufacturer    = trim(strip_tags($product->manufacturer ?? '')) !== '';
        $hasWarranty        = trim(strip_tags($product->warranty ?? '')) !== '';
        $hasReturnPolicy    = trim(strip_tags($product->return_policy ?? '')) !== '';
        $hasDetailsTab      = $hasFeatures || $hasCare || $hasIngredients || $hasSafety || $hasManufacturer || $hasWarranty || $hasReturnPolicy;
    @endphp
    <div class="mb-10" x-data="{ activeTab: 'description' }">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-6 overflow-x-auto scrollbar-hide -mb-px">
                <button @click="activeTab = 'description'"
                    :class="activeTab === 'description' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 border-b-2 font-semibold text-sm whitespace-nowrap transition">
                    Description
                </button>
                @if ($hasDetailsTab)
                    <button @click="activeTab = 'details'"
                        :class="activeTab === 'details' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 border-b-2 font-semibold text-sm whitespace-nowrap transition">
                        Details & Care
                    </button>
                @endif
                <button @click="activeTab = 'specs'"
                    :class="activeTab === 'specs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 border-b-2 font-semibold text-sm whitespace-nowrap transition">
                    Specifications
                </button>
                @if ($product->video_url)
                    <button @click="activeTab = 'video'"
                        :class="activeTab === 'video' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 border-b-2 font-semibold text-sm whitespace-nowrap transition">
                        Video
                    </button>
                @endif
                <button @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 border-b-2 font-semibold text-sm whitespace-nowrap transition">
                    Reviews ({{ $product->review_count }})
                </button>
            </nav>
        </div>

        {{-- Tab: Description --}}
        <div x-show="activeTab === 'description'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @if ($product->description)
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! $product->description !!}
                </div>
            @else
                <p class="text-gray-500 text-sm">No description available for this product.</p>
            @endif
        </div>

        {{-- Tab: Details & Care --}}
        @if ($hasDetailsTab)
            <div x-show="activeTab === 'details'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid md:grid-cols-2 gap-6">
                    @if ($hasFeatures)
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Key Features
                            </h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! $product->features !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasCare)
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                Care Instructions
                            </h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! $product->care_instructions !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasIngredients)
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Ingredients
                            </h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! $product->ingredients !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasManufacturer)
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Manufacturer
                            </h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! $product->manufacturer !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasWarranty)
                        <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                            <h3 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Warranty
                            </h3>
                            <div class="prose prose-sm max-w-none text-blue-800">
                                {!! $product->warranty !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasReturnPolicy)
                        <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                            <h3 class="font-bold text-green-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Return Policy
                            </h3>
                            <div class="prose prose-sm max-w-none text-green-800">
                                {!! $product->return_policy !!}
                            </div>
                        </div>
                    @endif

                    @if ($hasSafety)
                        <div class="bg-red-50 rounded-xl p-5 border border-red-100">
                            <h3 class="font-bold text-red-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Safety Warning
                            </h3>
                            <div class="prose prose-sm max-w-none text-red-700">
                                {!! $product->safety_warning !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Tab: Specifications --}}
        <div x-show="activeTab === 'specs'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @if ($product->sku)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">SKU</td>
                                <td class="px-5 py-3 text-gray-800">{{ $product->sku }}</td>
                            </tr>
                        @endif
                        @if ($product->barcode)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Barcode</td>
                                <td class="px-5 py-3 text-gray-800">{{ $product->barcode }}</td>
                            </tr>
                        @endif
                        @if ($product->brand)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Brand</td>
                                <td class="px-5 py-3 text-gray-800">{{ $product->brand->name }}</td>
                            </tr>
                        @endif
                        @if ($product->category)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Category</td>
                                <td class="px-5 py-3 text-gray-800">{{ $product->category->name }}</td>
                            </tr>
                        @endif

                        @if ($product->weight)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Weight</td>
                                <td class="px-5 py-3 text-gray-800">{{ $product->weight }} kg</td>
                            </tr>
                        @endif
                        @if ($product->length || $product->width || $product->height)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Dimensions</td>
                                <td class="px-5 py-3 text-gray-800">
                                    {{ collect([$product->length ? $product->length . 'cm' : null, $product->width ? $product->width . 'cm' : null, $product->height ? $product->height . 'cm' : null])->filter()->implode(' × ') }}
                                    <span class="text-gray-400 text-xs">(L × W × H)</span>
                                </td>
                            </tr>
                        @endif
                        @if ($product->delivery_type)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Delivery Type</td>
                                <td class="px-5 py-3 text-gray-800">{{ ucfirst($product->delivery_type) }}</td>
                            </tr>
                        @endif

                        @if ($product->product_type)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-500 bg-gray-50 w-1/3">Product Type</td>
                                <td class="px-5 py-3 text-gray-800">{{ ucfirst($product->product_type) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if ($product->tags && is_array($product->tags) && count($product->tags) > 0)
                    <div class="px-5 py-3 border-t border-gray-100">
                        <span class="text-sm font-medium text-gray-500 mr-2">Tags:</span>
                        @foreach ($product->tags as $tag)
                            <a href="{{ route('catalog') }}?search={{ urlencode($tag) }}"
                                class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full hover:bg-primary/10 hover:text-primary transition mr-1 mb-1">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Video --}}
        @if ($product->video_url)
            <div x-show="activeTab === 'video'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="aspect-video rounded-xl overflow-hidden bg-black max-w-3xl">
                    @php
                        $videoUrl = $product->video_url;
                        $embedUrl = $videoUrl;
                        if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                            if (str_contains($videoUrl, 'youtu.be')) {
                                $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            } else {
                                parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                                $embedUrl = "https://www.youtube.com/embed/" . ($params['v'] ?? '');
                            }
                        } elseif (str_contains($videoUrl, 'vimeo.com')) {
                            $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                            $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                        }
                    @endphp
                    <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            </div>
        @endif

        {{-- Tab: Reviews --}}
        <div x-show="activeTab === 'reviews'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @if ($product->review_count > 0)
                {{-- Rating Summary --}}
                <div class="flex items-center gap-6 mb-8 p-6 bg-gray-50 rounded-xl">
                    <div class="text-center">
                        <span class="text-4xl md:text-5xl font-bold text-primary-dark">{{ number_format($product->average_rating, 1) }}</span>
                        <div class="flex justify-center mt-2">
                            <x-shop.product.rating-stars :rating="$product->average_rating" :count="0" size="md" />
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $product->review_count }} {{ Str::plural('review', $product->review_count) }}</p>
                    </div>
                </div>

                {{-- Reviews List --}}
                <div class="space-y-5">
                    @foreach ($reviews as $review)
                        <div class="border-b border-gray-100 pb-5 last:border-0 last:pb-0">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                        <span class="text-primary font-bold text-sm">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <x-shop.product.rating-stars :rating="$review->rating" :count="0" size="xs" :compact="true" />
                                            @if ($review->is_verified_purchase)
                                                <span class="inline-flex items-center gap-1 text-xs text-green-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Verified
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            @if ($review->title)
                                <h4 class="font-semibold text-gray-800 mb-1 text-sm">{{ $review->title }}</h4>
                            @endif
                            <p class="text-gray-600 text-sm">{{ $review->content }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <x-shop.ui.empty-state icon="review" title="No reviews yet" message="Be the first to review this product!" />
            @endif

            {{-- Write a Review Form --}}
            @auth
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Write a Review</h3>

                    @if (session('review_success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                            {{ session('review_success') }}
                        </div>
                    @endif

                    @if (session('review_error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            {{ session('review_error') }}
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $product->slug) }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Star Rating --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating *</label>
                            <div x-data="{ rating: {{ old('rating', 0) }}, hover: 0 }" class="flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                        @click="rating = {{ $i }}"
                                        @mouseenter="hover = {{ $i }}"
                                        @mouseleave="hover = 0"
                                        class="p-0.5 focus:outline-none">
                                        <svg class="w-7 h-7 transition"
                                            :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" x-bind:value="rating">
                            </div>
                            @error('rating') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Review Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="Summarize your experience"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Content --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Review *</label>
                            <textarea name="content" rows="4" placeholder="What did you like or dislike about this product?"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-none">{{ old('content') }}</textarea>
                            @error('content') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-dark transition">
                            Submit Review
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-500">
                        <a href="{{ route('login') }}" class="text-primary font-medium hover:text-primary-dark">Sign in</a> to leave a review.
                    </p>
                </div>
            @endauth
        </div>
    </div>

    {{-- ===== Shipping Info ===== --}}
    @php
        $threshold   = $siteSettings['free_shipping_threshold'] ?? 0;
        $currency    = $siteSettings['currency'] ?? 'BDT';
        $currSym     = $currency === 'BDT' ? '৳' : ($currency === 'USD' ? '$' : $currency);
        $delivDays   = $siteSettings['delivery_days'] ?? '2-5 business days';
        $retDays     = $siteSettings['return_policy_days'] ?? '30';
    @endphp
    <section class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 mb-10">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Shipping & Delivery</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            @if ($threshold > 0)
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Free Shipping</h4>
                    <p class="text-xs text-gray-500 mt-0.5">On orders over {{ $currSym }}{{ number_format($threshold, 0) }}</p>
                </div>
            </div>
            @endif
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Fast Delivery</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $delivDays }}</p>
                </div>
            </div>
            @if ($retDays > 0)
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Easy Returns</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $retDays }}-day return policy</p>
                </div>
            </div>
            @endif
        </div>
    </section>

    {{-- ===== Related Products ===== --}}
    @if ($related->isNotEmpty())
        <section class="mb-12">
            <x-shop.ui.section-heading title="You May Also Like" view-all-url="{{ route('catalog') }}" />
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-5">
                @foreach ($related as $item)
                    <x-shop.product.card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
