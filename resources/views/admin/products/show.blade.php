@extends('admin.layouts.app')

@section('title', $product->name . ' — Product Details')

@section('content')
	{{-- Entity Header with Navigation Links --}}
	<x-admin.ui.entity-header
		:title="$product->name"
		:subtitle="'SKU: ' . ($product->sku ?? 'N/A')"
		:badge="$product->is_active ? 'Active' : 'Inactive'"
		:badgeColor="$product->is_active ? 'green' : 'gray'"
		:breadcrumbs="[
			['label' => 'Dashboard', 'url' => route('admin.dashboard')],
			['label' => 'Products', 'url' => route('admin.products.index')],
			['label' => $product->name],
		]"
		:actions="[
			['label' => 'Edit', 'url' => route('admin.products.edit', $product), 'primary' => true, 'icon' => '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\'/></svg>'],
		]"
		:links="[
			['label' => 'Overview', 'url' => route('admin.products.show', $product), 'active' => true],
			['label' => 'Variants', 'url' => '#variants', 'count' => $product->variants ? $product->variants->count() : 0],
			['label' => 'Stock History', 'url' => route('admin.inventory.movements.index', ['product_id' => $product->id])],
			['label' => 'Orders', 'url' => route('admin.orders.index', ['product_id' => $product->id])],
		]"
		backUrl="{{ route('admin.products.index') }}"
	/>

	@php
		// Calculate variants count once for reuse throughout the page
		$variantsCount = 0;
		$variantsCollection = collect();
		try {
			// Always use explicit query to ensure we get the data
			$variantsCollection = \App\Models\ProductVariant::where('product_id', $product->id)->get();
			$variantsCount = $variantsCollection->count();
		} catch (\Exception $e) {
			$variantsCount = 0;
			$variantsCollection = collect();
		}
	@endphp

	{{-- Stats Row --}}
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card
			title="Total Stock"
			:value="$variantsCount > 0 ? $variantsCollection->sum('stock_quantity') : $product->stock_quantity"
			subtitle="units available"
			icon="stock"
			:color="($variantsCount > 0 ? $variantsCollection->sum('stock_quantity') : $product->stock_quantity) > 0 ? 'green' : 'red'"
		/>
		<x-admin.ui.stat-card
			title="Selling Price"
			:value="'৳' . number_format($product->price, 2)"
			:subtitle="'Cost: ৳' . number_format($product->cost_price ?? 0, 2)"
			icon="currency"
			color="blue"
		/>
		<x-admin.ui.stat-card
			title="Profit Margin"
			:value="number_format($product->profit_margin, 1) . '%'"
			subtitle="per sale"
			icon="profit"
			:color="$product->profit_margin > 0 ? 'green' : 'red'"
		/>
		<x-admin.ui.stat-card
			title="Variants"
			:value="$variantsCount"
			subtitle="configurations"
			icon="box"
			color="purple"
			:url="$variantsCount > 0 ? '#variants' : null"
		/>
	</div>


	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Left Column - Main Content -->
		<div class="lg:col-span-2 space-y-6">
			<x-admin.ui.info-card
				title="Product Information"
				:columns="2"
				:items="[
					['label' => 'Category', 'value' => $product->category->name ?? '-', 'url' => $product->category ? route('admin.categories.show', $product->category) : null],
					['label' => 'Brand', 'value' => $product->brand->name ?? '-', 'url' => $product->brand ? route('admin.brands.show', $product->brand) : null],
					['label' => 'SKU', 'value' => $product->sku ?? '-', 'mono' => true],
					['label' => 'Barcode', 'value' => $product->barcode ?? '-', 'mono' => true],
					['label' => 'Type', 'value' => ucfirst($product->product_type), 'badge' => $product->product_type === 'variable' ? 'purple' : 'gray'],
					['label' => 'Status', 'value' => $product->is_active ? 'Active' : 'Inactive', 'badge' => $product->is_active ? 'green' : 'gray'],
				]"
			/>

			{{-- Images, Description --}}
			<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
				<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
					<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Images & Details</h4>
				</div>
				<div class="p-6 space-y-6">
					{{-- Images --}}
					@if ($product->images && count($product->images) > 0)
						<div>
							<h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Product Images</h5>
							<div class="grid grid-cols-4 gap-3">
								@foreach ($product->images as $image)
									<div class="relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700">
										<img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
										@if ($product->primary_image === $image)
											<span class="absolute top-2 left-2 bg-brand-500 text-white text-xs px-2 py-1 rounded">Primary</span>
										@endif
									</div>
								@endforeach
							</div>
						</div>
					@endif

					@if ($product->short_description)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Short Description</span>
							<p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->short_description }}</p>
						</div>
					@endif

					@if ($product->description)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Description</span>
							<div class="text-sm text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none prose-sm">
								{!! $product->description !!}
							</div>
						</div>
					@endif

					@if ($product->features)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Features</span>
							<div class="prose dark:prose-invert max-w-none prose-sm text-gray-600 dark:text-gray-300">
								{!! $product->features !!}
							</div>
						</div>
					@endif

					@if ($product->care_instructions)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Care Instructions</span>
							<div class="prose dark:prose-invert max-w-none prose-sm text-gray-600 dark:text-gray-300">
								{!! $product->care_instructions !!}
							</div>
						</div>
					@endif

					@if ($product->ingredients)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Ingredients</span>
							<div class="prose dark:prose-invert max-w-none prose-sm text-gray-600 dark:text-gray-300">
								{!! $product->ingredients !!}
							</div>
						</div>
					@endif

					@if ($product->safety_warning)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Safety Warning</span>
							<div class="prose dark:prose-invert max-w-none prose-sm text-gray-600 dark:text-gray-300">
								{!! $product->safety_warning !!}
							</div>
						</div>
					@endif

					@if ($product->return_policy)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Return Policy</span>
							<div class="prose dark:prose-invert max-w-none prose-sm text-gray-600 dark:text-gray-300">
								{!! $product->return_policy !!}
							</div>
						</div>
					@endif

					@if ($product->tags && count($product->tags) > 0)
						<div>
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Tags</span>
							<div class="flex flex-wrap gap-2">
								@foreach ($product->tags as $tag)
									<span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
										{{ $tag }}
									</span>
								@endforeach
							</div>
						</div>
					@endif

					@if ($product->warranty || $product->manufacturer)
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							@if ($product->warranty)
								<div>
									<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Warranty</span>
									<p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->warranty }}</p>
								</div>
							@endif
							@if ($product->manufacturer)
								<div>
									<span class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Manufacturer</span>
									<p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->manufacturer }}</p>
								</div>
							@endif
						</div>
					@endif
				</div>
			</div>

			{{-- Enhanced Variants Management (for variable products) --}}
		@if ($product->product_type === 'variable' || $variantsCount > 0)
			<div id="variants" x-data="variantManager()">
				{{-- Variant Generator Button --}}
				<div class="mb-4 flex items-center justify-between">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product Variants</h2>
					<x-admin.variant-generator :product="$product" :variant-attributes="$variantAttributes ?? []" />
				</div>

				{{-- Bulk Actions Bar --}}
				<div x-show="selectedVariants.length > 0"
						x-transition
						class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
						<div class="flex items-center justify-between flex-wrap gap-4">
							<div class="flex items-center gap-2">
								<span class="text-sm font-medium text-blue-900 dark:text-blue-100">
									<span x-text="selectedVariants.length"></span> variant(s) selected
								</span>
								<button @click="clearSelection()"
									class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
									Clear
								</button>
							</div>
							<div class="flex items-center gap-2 flex-wrap">
								<button @click="bulkAction('cost_price')"
									class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
									Set Cost Price
								</button>
								<button @click="bulkAction('price')"
									class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
									Set Regular Price
								</button>
								<button @click="bulkAction('sale')"
									class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
									Apply Discount
								</button>
								<button @click="bulkAction('stock')"
									class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
									Set Stock
								</button>
							</div>
						</div>
					</div>

					{{-- Variants Table --}}
					<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
								Product Variants ({{ $variantsCount }})
							</h3>
						</div>
						<div class="overflow-x-auto">
							<table class="w-full">
								<thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10 shadow-sm">
									<tr>
										<th class="px-4 py-3 text-left">
											<input type="checkbox"
												@change="toggleAll($event.target.checked)"
												class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
										</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Variant
										</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Attributes
										</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											SKU
										</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Cost Price
										</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Regular Price
										</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Sale Price
										</th>
										<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Discount
										</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Profit
										</th>
										<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Stock
										</th>
										<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Status
										</th>
										<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											Actions
										</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@php
										$variantsCollection = $product->relationLoaded('variants')
											? $product->getRelation('variants')
											: $product->variants()->get();
										$totalVariantStock = $variantsCollection->sum('stock_quantity');
										$lowStockCount = $variantsCollection->filter->is_low_stock->count();
										$inactiveVariantCount = $variantsCollection->where('is_active', false)->count();
									@endphp
									@foreach ($variantsCollection as $variant)
										@php
											$profitAmount = $variant->price - ($variant->cost_price ?? 0);
											$profitMargin = $variant->cost_price > 0 ? ($profitAmount / $variant->cost_price * 100) : 0;
											$hasDiscount = $variant->compare_at_price && $variant->compare_at_price > $variant->price;
											$discountPercent = $hasDiscount ? round((1 - $variant->price / $variant->compare_at_price) * 100) : 0;
										@endphp
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50"
											x-data="inlineEditor({{ $variant->id }}, {{ $variant->cost_price ?? 0 }}, {{ $variant->price }}, {{ $variant->stock_quantity }})">
											<td class="px-4 py-3">
												<input type="checkbox"
													:value="{{ $variant->id }}"
													@change="toggleVariant({{ $variant->id }}, $event.target.checked)"
													:checked="selectedVariants.includes({{ $variant->id }})"
													class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
											</td>
											<td class="px-4 py-3">
												<div class="text-sm font-medium text-gray-800 dark:text-white/90">
													{{ $variant->full_name }}
													@if ($variant->is_default)
														<x-admin.ui.badge color="blue" size="sm" class="ml-2">Default</x-admin.ui.badge>
													@endif
												</div>
											</td>
											<td class="px-4 py-3">
												@if ($variant->variantAttributes && $variant->variantAttributes->count())
													<div class="flex flex-wrap gap-2">
														@foreach ($variant->variantAttributes as $variantAttribute)
															<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
																{{ $variantAttribute->attribute->name ?? 'Attribute' }}:
																<span class="ml-1 font-semibold">{{ $variantAttribute->attributeValue->value ?? '-' }}</span>
															</span>
														@endforeach
													</div>
												@else
													<span class="text-xs text-gray-400">—</span>
												@endif
											</td>
											<td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">
												{{ $variant->sku }}
											</td>
											<td class="px-4 py-3 text-right" @click="startEdit('cost_price')">
												<div x-show="editing !== 'cost_price'" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 px-2 py-1 rounded">
													৳<span x-text="costPrice.toFixed(2)"></span>
												</div>
												<div x-show="editing === 'cost_price'" class="flex items-center gap-1">
													<input type="number" x-model="costPrice" step="0.01"
														@blur="saveField('cost_price')"
														@keydown.enter="saveField('cost_price')"
														@keydown.escape="cancelEdit()"
														x-ref="costPriceInput"
														class="w-24 px-2 py-1 text-sm text-right border rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600">
													<span x-show="saving === 'cost_price'" class="text-blue-600">
														<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
															<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
															<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
														</svg>
													</span>
													<span x-show="saved === 'cost_price'" class="text-green-600">✓</span>
												</div>
											</td>
											<td class="px-4 py-3 text-right" @click="startEdit('price')">
												<div x-show="editing !== 'price'" class="text-sm font-medium text-gray-800 dark:text-white/90 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 px-2 py-1 rounded">
													৳<span x-text="price.toFixed(2)"></span>
												</div>
												<div x-show="editing === 'price'" class="flex items-center gap-1">
													<input type="number" x-model="price" step="0.01"
														@blur="saveField('price')"
														@keydown.enter="saveField('price')"
														@keydown.escape="cancelEdit()"
														x-ref="priceInput"
														class="w-24 px-2 py-1 text-sm text-right border rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600">
													<span x-show="saving === 'price'" class="text-blue-600">
														<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
															<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
															<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
														</svg>
													</span>
													<span x-show="saved === 'price'" class="text-green-600">✓</span>
												</div>
											</td>
											<td class="px-4 py-3 text-right">
												@if($hasDiscount)
													<span class="text-sm font-semibold text-red-600 dark:text-red-400">
														৳{{ number_format($variant->price, 2) }}
													</span>
													<div class="text-xs text-gray-500 line-through">
														৳{{ number_format($variant->compare_at_price, 2) }}
													</div>
												@else
													<span class="text-xs text-gray-400">—</span>
												@endif
											</td>
											<td class="px-4 py-3 text-center">
												@if($hasDiscount)
													<x-admin.ui.badge color="red" size="sm">
														-{{ $discountPercent }}%
													</x-admin.ui.badge>
												@else
													<span class="text-xs text-gray-400">—</span>
												@endif
											</td>
											<td class="px-4 py-3 text-right">
												<div class="text-sm font-medium {{ $profitMargin >= 50 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
													৳{{ number_format($profitAmount, 2) }}
												</div>
												<div class="text-xs text-gray-500">
													({{ number_format($profitMargin, 0) }}%)
												</div>
											</td>
											<td class="px-4 py-3 text-center" @click="startEdit('stock')">
												<div x-show="editing !== 'stock'" class="cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 px-2 py-1 rounded inline-block">
													<x-admin.ui.badge
														:color="$variant->stock_quantity <= 0 ? 'red' : ($variant->is_low_stock ? 'orange' : 'green')"
													><span x-text="stock"></span></x-admin.ui.badge>
												</div>
												<div x-show="editing === 'stock'" class="flex items-center justify-center gap-1">
													<input type="number" x-model="stock" step="1"
														@blur="saveField('stock_quantity')"
														@keydown.enter="saveField('stock_quantity')"
														@keydown.escape="cancelEdit()"
														x-ref="stockInput"
														class="w-20 px-2 py-1 text-sm text-center border rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600">
													<span x-show="saving === 'stock_quantity'" class="text-blue-600">
														<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
															<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
															<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
														</svg>
													</span>
													<span x-show="saved === 'stock_quantity'" class="text-green-600">✓</span>
												</div>
											</td>
											<td class="px-4 py-3 text-center">
												<x-admin.ui.badge :color="$variant->is_active ? 'green' : 'gray'" size="sm">
													{{ $variant->is_active ? 'Active' : 'Inactive' }}
												</x-admin.ui.badge>
											</td>
											<td class="px-4 py-3 text-center">
												<a href="{{ route('admin.products.variants.show', [$product, $variant]) }}"
													class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
													<svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
													</svg>
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>

					{{-- Bulk Action Modal --}}
					<div x-show="showBulkModal"
						x-cloak
						@click.away="showBulkModal = false"
						class="fixed inset-0 z-50 overflow-y-auto"
						style="display: none;">
						<div class="flex items-center justify-center min-h-screen px-4">
							<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
							<div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
								<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="bulkModalTitle"></h3>

								<div class="mb-4">
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										<span x-text="bulkModalLabel"></span>
									</label>
									<input type="number"
										x-model="bulkValue"
										step="0.01"
										class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
								</div>

								<div x-show="bulkActionType === 'sale'" class="mb-4">
									<label class="flex items-center">
										<input type="checkbox" x-model="bulkAsPercentage" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
										<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Apply as percentage discount</span>
									</label>
								</div>

								<div class="flex justify-end gap-3">
									<button @click="showBulkModal = false"
										class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
										Cancel
									</button>
									<button @click="applyBulkAction()"
										class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
										Apply to <span x-text="selectedVariants.length"></span> Variants
									</button>
								</div>
							</div>
						</div>
					</div>

					@if ($variantsCollection->count())
						<div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 flex flex-wrap gap-3 text-sm text-gray-700 dark:text-gray-300">
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
								<strong>Total Stock:</strong> {{ number_format($totalVariantStock) }} units
							</span>
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
								<strong>Low Stock:</strong> {{ $lowStockCount }} variant{{ $lowStockCount === 1 ? '' : 's' }}
							</span>
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
								<strong>Inactive:</strong> {{ $inactiveVariantCount }} variant{{ $inactiveVariantCount === 1 ? '' : 's' }}
							</span>
						</div>
					@endif
				</div>

				<script>
					function variantManager() {
						return {
							selectedVariants: [],
							showBulkModal: false,
							bulkActionType: '',
							bulkValue: '',
							bulkAsPercentage: false,
							bulkModalTitle: '',
							bulkModalLabel: '',

							toggleVariant(id, checked) {
								if (checked) {
									this.selectedVariants.push(id);
								} else {
									this.selectedVariants = this.selectedVariants.filter(v => v !== id);
								}
							},

							toggleAll(checked) {
								if (checked) {
									this.selectedVariants = Array.from(document.querySelectorAll('tbody input[type="checkbox"]'))
										.map(cb => parseInt(cb.value));
								} else {
									this.selectedVariants = [];
								}
							},

							clearSelection() {
								this.selectedVariants = [];
							},

							bulkAction(type) {
								this.bulkActionType = type;
								this.bulkValue = '';
								this.bulkAsPercentage = false;

								const titles = {
									'cost_price': 'Set Cost Price',
									'price': 'Set Regular Price',
									'sale': 'Apply Discount',
									'stock': 'Set Stock Quantity'
								};

								const labels = {
									'cost_price': 'Cost Price (৳)',
									'price': 'Regular Price (৳)',
									'sale': 'Discount Amount (৳) or %',
									'stock': 'Stock Quantity'
								};

								this.bulkModalTitle = titles[type];
								this.bulkModalLabel = labels[type];
								this.showBulkModal = true;
							},

							applyBulkAction() {
								if (!this.bulkValue || this.selectedVariants.length === 0) {
									alert('Please enter a value');
									return;
								}

								// Send AJAX request to update variants
								fetch('{{ route("admin.products.variants.bulk-update", $product) }}', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
									},
									body: JSON.stringify({
										variant_ids: this.selectedVariants,
										action_type: this.bulkActionType,
										value: this.bulkValue,
										as_percentage: this.bulkAsPercentage
									})
								})
								.then(response => response.json())
								.then(data => {
									if (data.success) {
										window.location.reload();
									} else {
										alert('Error: ' + (data.message || 'Failed to update variants'));
									}
								})
								.catch(error => {
									console.error('Error:', error);
									alert('Failed to update variants');
								});
							}
						}
					}

					function inlineEditor(variantId, initialCost, initialPrice, initialStock) {
						return {
							variantId: variantId,
							costPrice: initialCost,
							price: initialPrice,
							stock: initialStock,
							editing: null,
							saving: null,
							saved: null,

							startEdit(field) {
								this.editing = field;
								this.$nextTick(() => {
									const inputRef = field === 'cost_price' ? 'costPriceInput' :
													field === 'price' ? 'priceInput' : 'stockInput';
									if (this.$refs[inputRef]) {
										this.$refs[inputRef].focus();
										this.$refs[inputRef].select();
									}
								});
							},

							cancelEdit() {
								this.editing = null;
								this.saving = null;
								this.saved = null;
							},

							async saveField(field) {
								if (this.saving) return;

								const fieldMap = {
									'cost_price': this.costPrice,
									'price': this.price,
									'stock_quantity': this.stock
								};

								const value = fieldMap[field];
								this.saving = field;

								try {
									const response = await fetch(`/admin/products/{{ $product->id }}/variants/${this.variantId}`, {
										method: 'PUT',
										headers: {
											'Content-Type': 'application/json',
											'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
										},
										body: JSON.stringify({ [field]: value })
									});

									const data = await response.json();

									if (data.success) {
										this.saved = field;
										setTimeout(() => {
											this.editing = null;
											this.saving = null;
											this.saved = null;
										}, 1000);
									} else {
										alert('Error: ' + (data.message || 'Failed to update'));
										this.cancelEdit();
									}
								} catch (error) {
									console.error('Error:', error);
									alert('Failed to update field');
									this.cancelEdit();
								}
							}
						}
					}
				</script>
			@endif
		</div>

		{{-- Right Column - Sidebar --}}
		<div class="space-y-6">
			{{-- Pricing Card --}}
			<x-admin.ui.info-card
				title="Pricing"
				:items="[
					['label' => 'Selling Price', 'value' => '৳' . number_format($product->price, 2), 'mono' => true],
					['label' => 'Cost Price', 'value' => '৳' . number_format($product->cost_price ?? 0, 2), 'mono' => true],
					['label' => 'Profit Margin', 'value' => number_format($product->profit_margin, 1) . '%', 'badge' => $product->profit_margin > 0 ? 'green' : 'red'],
				]"
			/>

			{{-- Stock Card (Simple products only) --}}
			@if ($variantsCount === 0)
				<x-admin.ui.info-card
					title="Inventory"
					:items="[
						['label' => 'Stock Quantity', 'value' => $product->stock_quantity, 'badge' => $product->stock_quantity <= 0 ? 'red' : ($product->stock_quantity <= ($product->low_stock_alert ?? 0) ? 'orange' : 'green')],
						['label' => 'Low Stock Alert', 'value' => $product->low_stock_alert ?? 'Not set'],
						['label' => 'Stock Status', 'value' => $product->stock_status ? str_replace('_', ' ', ucfirst($product->stock_status)) : 'In Stock', 'badge' => ($product->stock_status ?? 'in_stock') === 'in_stock' ? 'green' : 'red'],
					]"
				/>
			@endif

			{{-- Physical Attributes --}}
			@if ($product->weight || $product->length || $product->width || $product->height)
				<x-admin.ui.info-card
					title="Dimensions"
					:items="array_filter([
						$product->weight ? ['label' => 'Weight', 'value' => $product->weight . ' kg'] : null,
						($product->length && $product->width && $product->height) ? ['label' => 'L × W × H', 'value' => $product->length . ' × ' . $product->width . ' × ' . $product->height . ' cm'] : null,
					])"
				/>
			@endif

			{{-- Metadata --}}
			<x-admin.ui.info-card
				title="Metadata"
				:items="[
					['label' => 'Created', 'value' => $product->created_at->format('M d, Y')],
					['label' => 'Updated', 'value' => $product->updated_at->format('M d, Y')],
					['label' => 'Featured', 'value' => $product->is_featured ? 'Yes' : 'No', 'badge' => $product->is_featured ? 'blue' : 'gray'],
				]"
			/>
		</div>
	</div>
@endsection
