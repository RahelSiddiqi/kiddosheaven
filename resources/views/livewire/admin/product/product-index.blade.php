<div>
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Products</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your product catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Product
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-admin.ui.stat-card
            title="Total Products"
            :value="$totalProducts"
            icon="box"
            color="blue"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Active Products"
            :value="$activeProducts"
            icon="check"
            color="green"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Low Stock"
            :value="$lowStockProducts"
            icon="alert"
            :color="$lowStockProducts > 0 ? 'orange' : 'green'"
            :compact="true"
        />
    </div>

    {{-- Filters & Search --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                {{-- Search --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search products..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 sm:w-64"
                    />
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="status"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="featured">Featured</option>
                    <option value="low-stock">Low Stock</option>
                </select>

                {{-- Sort --}}
                <select
                    wire:model.live="sort"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name">Name A-Z</option>
                    <option value="stock-low">Stock: Low to High</option>
                    <option value="stock-high">Stock: High to Low</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        @if($products->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $status)
                        Try adjusting your filters or search term.
                    @else
                        Get started by adding your first product.
                    @endif
                </p>
                @if(!$search && !$status)
                    <a href="{{ route('admin.products.create') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-brand-500 hover:text-brand-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add your first product
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 w-10">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAll"
                                    wire:click="toggleSelectAll"
                                    class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Image</th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Name / SKU</th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Category</th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Price</th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right text-sm font-normal text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]" wire:key="product-{{ $product->id }}">
                                <td class="px-4 py-4">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedIds"
                                        value="{{ $product->id }}"
                                        class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700"
                                    />
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $img = $product->primary_image ?? ($product->images[0] ?? null);
                                    @endphp
                                    @if($img)
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                    <div class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $product->category->name ?? '-' }}</div>
                                    @if($product->brand)
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $product->brand->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($product->price, 2) }}</div>
                                    @if($product->discount_price)
                                        <div class="text-xs text-red-500 line-through">{{ number_format($product->discount_price, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $variantsCount = $product->variants?->count() ?? 0;
                                        $stockTotal = $variantsCount > 0 ? $product->variants->sum('stock_quantity') : ($product->stock_quantity ?? 0);
                                        $stockClass = $stockTotal <= 0 ? 'text-red-600 dark:text-red-400' : ($stockTotal <= 10 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400');
                                    @endphp
                                    <span class="text-sm font-medium {{ $stockClass }}">{{ number_format($stockTotal) }}</span>
                                    @if($stockTotal <= 0)
                                        <div class="text-xs text-red-500">Out of stock</div>
                                    @elseif($stockTotal <= 10)
                                        <div class="text-xs text-yellow-500">Low stock</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if($product->is_active)
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                Inactive
                                            </span>
                                        @endif
                                        @if($product->is_featured)
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
