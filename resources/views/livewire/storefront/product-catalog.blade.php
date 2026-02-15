<div class="space-y-6">
    {{-- Mobile Filter & Sort Buttons --}}
    <div class="flex gap-3 mb-4 lg:hidden">
        <button wire:click="toggleFilterDrawer"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-medium shadow-md bg-primary text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filters
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Sidebar Filters (Desktop) --}}
        <aside class="hidden lg:block lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit sticky top-24">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900">Filters</h3>
                <button wire:click="clearFilters" class="text-primary text-sm hover:underline">Clear all</button>
            </div>

            {{-- Categories --}}
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3">Categories</h4>
                <div class="space-y-1">
                    <button wire:click="setCategory(null)"
                        class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ !$categoryId ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        All Products
                    </button>
                    @foreach ($categories as $category)
                        <button wire:click="setCategory({{ $category->id }})"
                            class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $categoryId == $category->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Brands --}}
            @if ($brands->isNotEmpty())
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Brands</h4>
                    <div class="space-y-1">
                        @foreach ($brands as $brand)
                            <button wire:click="setBrand({{ $brand->id }})"
                                class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $brandId == $brand->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $brand->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Price Range --}}
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3">Price Range</h4>
                <div class="space-y-1">
                    <button wire:click="setPriceRange('under-10')"
                        class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $priceRange == 'under-10' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Under ৳1,000
                    </button>
                    <button wire:click="setPriceRange('10-25')"
                        class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $priceRange == '10-25' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        ৳1,000 - ৳2,500
                    </button>
                    <button wire:click="setPriceRange('25-50')"
                        class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $priceRange == '25-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        ৳2,500 - ৳5,000
                    </button>
                    <button wire:click="setPriceRange('over-50')"
                        class="w-full text-left px-3 py-2 rounded-lg transition text-sm {{ $priceRange == 'over-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Over ৳5,000
                    </button>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="lg:col-span-9">
            {{-- Sort Bar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 mb-6 flex items-center justify-between">
                <h3 class="font-bold text-lg text-gray-900">Sort By</h3>
                <div class="flex gap-2">
                    <button wire:click="setSort('newest')"
                        class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ $sortBy == 'newest' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Newest
                    </button>
                    <button wire:click="setSort('price-low')"
                        class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ $sortBy == 'price-low' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Price: Low-High
                    </button>
                    <button wire:click="setSort('price-high')"
                        class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ $sortBy == 'price-high' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Price: High-Low
                    </button>
                    <button wire:click="setSort('popular')"
                        class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ $sortBy == 'popular' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Popular
                    </button>
                </div>
            </div>

            {{-- Products Grid --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <span class="text-5xl mb-4 block">🔍</span>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your filters</p>
                    <button wire:click="clearFilters"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition">
                        Clear Filters
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Mobile Filter Drawer --}}
    @if ($isFilterDrawerOpen)
        <div class="fixed inset-0 z-[10000] lg:hidden">
            <div class="fixed inset-0 bg-black/60" wire:click="toggleFilterDrawer"></div>
            <div class="fixed top-0 left-0 h-full w-4/5 max-w-xs bg-white overflow-y-auto shadow-xl p-6 z-[10001]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-xl text-gray-900">Filters</h3>
                    <button wire:click="toggleFilterDrawer" class="p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Same filter content as desktop --}}
                <div class="space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Categories</h4>
                        <div class="space-y-1">
                            <button wire:click="setCategory(null); toggleFilterDrawer()"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm {{ !$categoryId ? 'bg-primary/10 text-primary' : 'text-gray-600' }}">
                                All Products
                            </button>
                            @foreach ($categories as $category)
                                <button wire:click="setCategory({{ $category->id }}); toggleFilterDrawer()"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm {{ $categoryId == $category->id ? 'bg-primary/10 text-primary' : 'text-gray-600' }}">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
