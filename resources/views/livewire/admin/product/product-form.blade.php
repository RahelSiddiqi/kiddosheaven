<div x-data="{ activeTab: @entangle('activeTab') }">
    {{-- Breadcrumb Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.products.index') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition">Products</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 dark:text-white">{{ $productId ? 'Edit: ' . $name : 'Create Product' }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $productId ? 'Edit Product' : 'Create Product' }}</h1>
    </div>

    <form wire:submit="save">
        {{-- Sticky Top Bar --}}
        <div class="sticky top-0 z-30 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 -mx-4 md:-mx-6 px-4 md:px-6 py-4 mb-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Product Name Input --}}
                <div class="flex-1 max-w-xl">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="name"
                        placeholder="Product name *"
                        class="w-full text-lg font-semibold rounded-lg border border-gray-300 bg-white px-4 py-2.5 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Toggles & Actions --}}
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="isActive" class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700" />
                        <span class="text-sm text-gray-700 dark:text-gray-200">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="isFeatured" class="w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500 dark:border-gray-600 dark:bg-gray-700" />
                        <span class="text-sm text-gray-700 dark:text-gray-200">Featured</span>
                    </label>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-teal-600 text-sm font-medium text-white hover:bg-teal-700 disabled:opacity-60 transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">{{ $productId ? 'Update' : 'Create' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-1 -mb-px overflow-x-auto">
                @foreach(['basic' => 'Basic', 'pricing' => 'Pricing', 'inventory' => 'Inventory', 'media' => 'Media', 'seo' => 'SEO & Tags', 'additional' => 'Additional'] as $tab => $label)
                    <button
                        type="button"
                        @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}'
                            ? 'border-teal-500 text-teal-600 dark:text-teal-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Panels --}}
        <div class="space-y-6">

            {{-- ═══════════ BASIC TAB ═══════════ --}}
            <div x-show="activeTab === 'basic'" x-cloak class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Slug --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Slug</label>
                            <input
                                type="text"
                                wire:model="slug"
                                @if($productId) readonly @endif
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 @if($productId) bg-gray-50 dark:bg-gray-900 cursor-not-allowed @endif"
                            />
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">SKU</label>
                            <input type="text" wire:model="sku" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Barcode --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Barcode</label>
                            <input type="text" wire:model="barcode" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Category <span class="text-red-500">*</span></label>
                            <select wire:model="categoryId" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">Select category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Brand</label>
                            <select wire:model="brandId" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">No brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Product Type</label>
                            <select wire:model="productType" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="simple">Simple</option>
                                <option value="variable">Variable</option>
                                <option value="digital">Digital</option>
                            </select>
                        </div>

                        {{-- Delivery Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Delivery Type</label>
                            <select wire:model="deliveryType" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="instant">Instant</option>
                                <option value="schedule">Scheduled</option>
                                <option value="frozen">Frozen</option>
                            </select>
                        </div>
                    </div>

                    {{-- Short Description --}}
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Short Description</label>
                        <textarea wire:model="shortDescription" rows="2" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 resize-none"></textarea>
                    </div>

                    {{-- Description --}}
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Full Description</label>
                        <textarea wire:model="description" rows="6" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════ PRICING TAB ═══════════ --}}
            <div x-show="activeTab === 'pricing'" x-cloak class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Pricing</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        {{-- Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Price <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">RM</span>
                                <input type="number" step="0.01" wire:model="price" class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Cost Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Cost Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">RM</span>
                                <input type="number" step="0.01" wire:model="costPrice" class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                            @error('costPrice') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Profit Margin (calculated) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Profit Margin</label>
                            <div class="h-10 flex items-center px-3 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm">
                                @if($price && $costPrice && (float)$price > 0)
                                    @php $margin = (((float)$price - (float)$costPrice) / (float)$price) * 100; @endphp
                                    <span class="{{ $margin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>

                        {{-- Discount Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Discount Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">RM</span>
                                <input type="number" step="0.01" wire:model="discountPrice" class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                        </div>

                        {{-- Discount Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Discount Type</label>
                            <select wire:model="discountType" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>

                        {{-- VAT Rate --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">VAT Rate (%)</label>
                            <input type="number" step="0.01" wire:model="vatRate" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Wholesale Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Wholesale Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">RM</span>
                                <input type="number" step="0.01" wire:model="wholesalePrice" class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ INVENTORY TAB ═══════════ --}}
            <div x-show="activeTab === 'inventory'" x-cloak class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Stock Management</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        {{-- Stock Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="stockQuantity" min="0" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            @error('stockQuantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Low Stock Alert --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Low Stock Alert</label>
                            <input type="number" wire:model="lowStockAlert" min="0" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Stock Status --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Stock Status</label>
                            <select wire:model="stockStatus" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="pre_order">Pre-Order</option>
                                <option value="backorder">Backorder</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Dimensions & Weight</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                        {{-- Weight --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Weight (g)</label>
                            <input type="number" step="0.01" wire:model="weight" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Length --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Length (cm)</label>
                            <input type="number" step="0.01" wire:model="length" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Width --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Width (cm)</label>
                            <input type="number" step="0.01" wire:model="width" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Height --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Height (cm)</label>
                            <input type="number" step="0.01" wire:model="height" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ MEDIA TAB ═══════════ --}}
            <div x-show="activeTab === 'media'" x-cloak class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Product Images</h2>

                    {{-- Existing Images Grid --}}
                    @if(count($existingImages) > 0)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Current Images</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($existingImages as $img)
                                    <div class="relative group aspect-square rounded-lg overflow-hidden border-2 {{ $primaryImage === $img ? 'border-teal-500' : 'border-gray-200 dark:border-gray-700' }}">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Product image" class="w-full h-full object-cover" />

                                        {{-- Primary indicator --}}
                                        @if($primaryImage === $img)
                                            <div class="absolute top-1 left-1 bg-teal-500 text-white text-xs px-2 py-0.5 rounded">
                                                Primary
                                            </div>
                                        @endif

                                        {{-- Action overlay --}}
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            @if($primaryImage !== $img)
                                                <button type="button" wire:click="setPrimaryImage('{{ $img }}')" class="p-2 bg-white rounded-full text-teal-600 hover:bg-teal-50 transition" title="Set as primary">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                </button>
                                            @endif
                                            <button type="button" wire:click="queueDeleteImage('{{ $img }}')" wire:confirm="Are you sure you want to remove this image?" class="p-2 bg-white rounded-full text-red-600 hover:bg-red-50 transition" title="Remove image">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- New Image Uploads --}}
                    @if(count($newImages) > 0)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">New Images (pending upload)</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($newImages as $index => $img)
                                    <div class="relative group aspect-square rounded-lg overflow-hidden border-2 border-dashed border-teal-300 dark:border-teal-600">
                                        <img src="{{ $img->temporaryUrl() }}" alt="New image" class="w-full h-full object-cover" />
                                        <button type="button" wire:click="removeNewImage({{ $index }})" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload Input --}}
                    <div
                        x-data="{ isDragging: false }"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                        :class="isDragging ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20' : 'border-gray-300 dark:border-gray-600'"
                        class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer hover:border-teal-500 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                    >
                        <input type="file" wire:model="newImages" multiple accept="image/*" class="hidden" x-ref="fileInput" id="image-upload" />
                        <label for="image-upload" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-teal-600 dark:text-teal-400">Click to upload</span> or drag and drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">PNG, JPG, GIF, WEBP up to 20MB each</p>
                        </label>
                    </div>

                    <div wire:loading wire:target="newImages" class="mt-3 flex items-center gap-2 text-sm text-teal-600 dark:text-teal-400">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Uploading images...
                    </div>

                    @error('newImages.*') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Video URL --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Video</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Video URL (YouTube/Vimeo)</label>
                        <input type="url" wire:model="videoUrl" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        @error('videoUrl') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ═══════════ SEO & TAGS TAB ═══════════ --}}
            <div x-show="activeTab === 'seo'" x-cloak class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">SEO Settings</h2>

                    <div class="space-y-5">
                        {{-- Meta Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Meta Title</label>
                            <input type="text" wire:model="metaTitle" placeholder="Defaults to product name" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            @error('metaTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Meta Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Meta Description</label>
                            <textarea wire:model="metaDescription" rows="3" placeholder="Brief description for search engines (recommended: 150-160 characters)" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 resize-none"></textarea>
                            @if($metaDescription)
                                <p class="mt-1 text-xs {{ strlen($metaDescription) > 160 ? 'text-orange-500' : 'text-gray-500' }}">
                                    {{ strlen($metaDescription) }}/160 characters
                                </p>
                            @endif
                            @error('metaDescription') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Slug Display --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">URL Slug</label>
                            <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm">
                                <span class="text-gray-500 dark:text-gray-400">/product/</span>
                                <span class="font-mono text-gray-800 dark:text-gray-200">{{ $slug ?: 'your-product-slug' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Tags</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Product Tags</label>
                        <input type="text" wire:model="tagsInput" placeholder="baby toys, educational, wooden (comma-separated)" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">Separate tags with commas</p>
                    </div>

                    @if($tagsInput)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach(array_filter(array_map('trim', explode(',', $tagsInput))) as $tag)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════ ADDITIONAL TAB ═══════════ --}}
            <div x-show="activeTab === 'additional'" x-cloak class="space-y-6">
                {{-- Extended Content --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Extended Content</h2>

                    <div class="space-y-5">
                        {{-- Features --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Features</label>
                            <textarea wire:model="features" rows="4" placeholder="List product features..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>
                        </div>

                        {{-- Care Instructions --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Care Instructions</label>
                            <textarea wire:model="careInstructions" rows="3" placeholder="How to care for this product..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>
                        </div>

                        {{-- Ingredients --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Ingredients / Materials</label>
                            <textarea wire:model="ingredients" rows="3" placeholder="Product ingredients or materials..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>
                        </div>

                        {{-- Safety Warning --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Safety Warning</label>
                            <textarea wire:model="safetyWarning" rows="2" placeholder="Any safety warnings or age restrictions..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Certifications --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Certifications</h2>

                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="halalCertified" class="w-5 h-5 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700" />
                            <span class="text-sm text-gray-700 dark:text-gray-200">Halal Certified</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="organicCertified" class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700" />
                            <span class="text-sm text-gray-700 dark:text-gray-200">Organic Certified</span>
                        </label>
                    </div>
                </div>

                {{-- Policies & Manufacturer --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Policies & Manufacturer</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Manufacturer --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Manufacturer</label>
                            <input type="text" wire:model="manufacturer" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Warranty --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Warranty</label>
                            <input type="text" wire:model="warranty" placeholder="e.g., 1 Year Manufacturer Warranty" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        </div>

                        {{-- Return Policy --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Return Policy</label>
                            <textarea wire:model="returnPolicy" rows="2" placeholder="Describe return policy for this product..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Save Bar (Mobile) --}}
        <div class="sm:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 p-4 z-30">
            <button type="submit" wire:loading.attr="disabled" class="w-full px-5 py-3 rounded-lg bg-teal-600 text-sm font-medium text-white hover:bg-teal-700 disabled:opacity-60 transition flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">{{ $productId ? 'Update Product' : 'Create Product' }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Saving...
                </span>
            </button>
        </div>

        {{-- Spacer for mobile bottom bar --}}
        <div class="sm:hidden h-20"></div>
    </form>
</div>
