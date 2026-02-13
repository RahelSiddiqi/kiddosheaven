@extends('admin.layouts.app')

@section('title', $variant->full_name . ' — Variant Details')

@section('content')
	@php
		$inputClasses = 'block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500';
	@endphp

	<x-admin.ui.entity-header
		:title="$variant->full_name"
		:subtitle="'SKU: ' . ($variant->sku ?? 'N/A')"
		:badge="$variant->is_active ? 'Active' : 'Inactive'"
		:badgeColor="$variant->is_active ? 'green' : 'gray'"
		:breadcrumbs="[
		    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
		    ['label' => 'Products', 'url' => route('admin.products.index')],
		    ['label' => $product->name, 'url' => route('admin.products.show', $product)],
		    ['label' => 'Variants', 'url' => route('admin.products.show', $product) . '#variants'],
		    ['label' => $variant->full_name],
		]"
	/>

	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card
			title="Current Stock"
			:value="$variant->stock_quantity"
			subtitle="units available"
			icon="stock"
			:color="$variant->stock_quantity > 0 ? 'green' : 'red'"
			:url="route('admin.inventory.movements.index', ['variant_id' => $variant->id])"
		/>
		<x-admin.ui.stat-card
			title="Selling Price"
			:value="'৳' . number_format($variant->price, 2)"
			:subtitle="'Cost: ৳' . number_format($variant->cost_price, 2)"
			icon="currency"
			color="blue"
		/>
		<x-admin.ui.stat-card
			title="Profit per Unit"
			:value="'৳' . number_format($variant->price - $variant->cost_price, 2)"
			:subtitle="number_format((($variant->price - $variant->cost_price) / $variant->price) * 100, 1) . '% margin'"
			icon="profit"
			:color="$variant->price - $variant->cost_price > 0 ? 'green' : 'red'"
		/>
		<x-admin.ui.stat-card
			title="Total Orders"
			value="0"
			subtitle="all time"
			icon="cart"
			color="purple"
			:url="route('admin.orders.index', ['variant_id' => $variant->id])"
		/>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<div class="lg:col-span-2 space-y-6">
			<x-admin.ui.info-card
				title="Variant Information"
				:columns="2"
				:items="[
			    ['label' => 'Product', 'value' => $product->name, 'url' => route('admin.products.show', $product)],
			    ['label' => 'Variant Name', 'value' => $variant->full_name],
			    ['label' => 'SKU', 'value' => $variant->sku ?? '-', 'mono' => true],
			    ['label' => 'Barcode', 'value' => $variant->barcode ?? '-', 'mono' => true],
			    [
			        'label' => 'Status',
			        'value' => $variant->is_active ? 'Active' : 'Inactive',
			        'badge' => $variant->is_active ? 'green' : 'gray',
			    ],
			    [
			        'label' => 'Default Variant',
			        'value' => $variant->is_default ? 'Yes' : 'No',
			        'badge' => $variant->is_default ? 'blue' : 'gray',
			    ],
			]"
			/>

			@if ($variant->variantAttributes && $variant->variantAttributes->count() > 0)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Variant Attributes</h4>
					</div>
					<div class="p-6">
						<div class="grid grid-cols-2 gap-4">
							@foreach ($variant->variantAttributes as $attr)
								<div>
									<span class="text-sm text-gray-500 dark:text-gray-400">{{ $attr->attribute->name ?? 'Attribute' }}</span>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $attr->attributeValue->value ?? '-' }}</p>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			@endif

			<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
				<div class="px-5 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-800">
					<div>
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Quick Edit</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">Update core details and status.</p>
					</div>
					<a href="{{ route('admin.products.show', $product) }}#variants" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Back to product variants</a>
				</div>
				<div class="p-5">
					<form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" class="space-y-4">
						@csrf
						@method('PUT')
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">Name</span>
								<input name="name" value="{{ old('name', $variant->full_name) }}" class="{{ $inputClasses }}" />
							</label>
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">SKU</span>
								<input name="sku" value="{{ old('sku', $variant->sku) }}" class="{{ $inputClasses }}" />
							</label>
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">Price</span>
								<input type="number" step="0.01" name="price" value="{{ old('price', $variant->price) }}" class="{{ $inputClasses }}" />
							</label>
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">Cost Price</span>
								<input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $variant->cost_price) }}" class="{{ $inputClasses }}" />
							</label>
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">Compare at Price</span>
								<input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $variant->compare_at_price) }}" class="{{ $inputClasses }}" />
							</label>
							<label class="space-y-1">
								<span class="text-gray-700 dark:text-gray-300">Stock Qty</span>
								<input type="number" name="stock_quantity" value="{{ old('stock_quantity', $variant->stock_quantity) }}" class="{{ $inputClasses }}" />
							</label>
						</div>
						<div class="flex items-center gap-6 text-sm">
							<label class="inline-flex items-center gap-2">
								<input type="checkbox" name="is_active" value="1" {{ old('is_active', $variant->is_active) ? 'checked' : '' }}
									class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
								<span class="text-gray-700 dark:text-gray-300">Active</span>
							</label>
							<label class="inline-flex items-center gap-2">
								<input type="checkbox" name="is_default" value="1" {{ old('is_default', $variant->is_default) ? 'checked' : '' }}
									class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
								<span class="text-gray-700 dark:text-gray-300">Default</span>
							</label>
						</div>
						<div class="flex justify-end">
							<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Save Variant</button>
						</div>
					</form>
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Add Stock (batch)</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">Create a purchase batch for this variant.</p>
					</div>
					<div class="p-5">
						<form method="POST" action="{{ route('admin.purchase-batches.store') }}" class="space-y-4 text-sm">
							@csrf
							<input type="hidden" name="product_id" value="{{ $product->id }}">
							<input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
							<div class="grid grid-cols-1 gap-4">
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Quantity</span>
									<input type="number" name="quantity" min="1" required class="{{ $inputClasses }}">
								</label>
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Unit Cost</span>
									<input type="number" step="0.01" name="unit_cost" required class="{{ $inputClasses }}">
								</label>
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Batch Number (optional)</span>
									<input type="text" name="batch_number" placeholder="Auto-generated if empty" class="{{ $inputClasses }}">
								</label>
							</div>
							<div class="flex justify-end">
								<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Add Stock</button>
							</div>
						</form>
					</div>
				</div>

				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Record Inventory Movement</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">Log an adjustment, return, or transfer.</p>
					</div>
					<div class="p-5">
						<form method="POST" action="{{ route('admin.inventory.movements.store') }}" class="space-y-4 text-sm">
							@csrf
							<input type="hidden" name="product_id" value="{{ $product->id }}">
							<input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
							<div class="grid grid-cols-1 gap-4">
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Type</span>
									<select name="movement_type" class="{{ $inputClasses }}">
										<option value="purchase">Purchase (in)</option>
										<option value="adjustment">Adjustment (in/out)</option>
										<option value="return">Return (in)</option>
										<option value="transfer">Transfer (out)</option>
									</select>
								</label>
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Quantity</span>
									<input type="number" name="quantity" min="1" required class="{{ $inputClasses }}">
								</label>
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Unit Cost (optional)</span>
									<input type="number" step="0.01" name="unit_cost" class="{{ $inputClasses }}">
								</label>
								<label class="space-y-1">
									<span class="text-gray-700 dark:text-gray-300">Notes</span>
									<textarea name="notes" rows="2" class="{{ $inputClasses }}"></textarea>
								</label>
							</div>
							<div class="flex justify-end">
								<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Record Movement</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			@if ($variant->image)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Variant Image</h4>
					</div>
					<div class="p-6">
						<img src="{{ asset('storage/' . $variant->image) }}" alt="{{ $variant->full_name }}" class="w-64 h-64 object-cover rounded-lg">
					</div>
				</div>
			@endif
		</div>

		<div class="space-y-6">
			<x-admin.ui.info-card
				title="Pricing"
				:items="[
			    ['label' => 'Selling Price', 'value' => '৳' . number_format($variant->price, 2), 'mono' => true],
			    ['label' => 'Cost Price (FIFO)', 'value' => '৳' . number_format($variant->cost_price, 2), 'mono' => true],
			    [
			        'label' => 'Profit per Unit',
			        'value' => '৳' . number_format($variant->price - $variant->cost_price, 2),
			        'mono' => true,
			        'badge' => $variant->price - $variant->cost_price > 0 ? 'green' : 'red',
			    ],
			    [
			        'label' => 'Profit Margin',
			        'value' => number_format((($variant->price - $variant->cost_price) / $variant->price) * 100, 1) . '%',
			        'badge' => $variant->price - $variant->cost_price > 0 ? 'green' : 'red',
			    ],
			]"
			/>

			<x-admin.ui.info-card
				title="Inventory"
				:items="[
			    [
			        'label' => 'Stock Quantity',
			        'value' => $variant->stock_quantity,
			        'badge' => $variant->stock_quantity <= 0 ? 'red' : ($variant->is_low_stock ? 'orange' : 'green'),
			    ],
			    ['label' => 'Low Stock Alert', 'value' => $variant->low_stock_alert ?? 'Not set'],
			    [
			        'label' => 'Stock Status',
			        'value' => $variant->stock_quantity > 0 ? 'In Stock' : 'Out of Stock',
			        'badge' => $variant->stock_quantity > 0 ? 'green' : 'red',
			    ],
			]"
			/>

			<x-admin.ui.info-card
				title="Metadata"
				:items="[
			    ['label' => 'Created', 'value' => $variant->created_at->format('M d, Y')],
			    ['label' => 'Updated', 'value' => $variant->updated_at->format('M d, Y')],
			]"
			/>
		</div>
	</div>
@endsection