@extends('admin.layouts.app')

@section('title', $variant->full_name . ' — Variant Details')

@section('content')
	{{-- Entity Header with Navigation Links --}}
	<x-admin.ui.entity-header :title="$variant->full_name" :subtitle="'SKU: ' . ($variant->sku ?? 'N/A')" :badge="$variant->is_active ? 'Active' : 'Inactive'" :badgeColor="$variant->is_active ? 'green' : 'gray'" :breadcrumbs="[
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Products', 'url' => route('admin.products.index')],
	    ['label' => $product->name, 'url' => route('admin.products.show', $product)],
	    ['label' => $variant->full_name],
	]"
		:actions="[
		    [
		        'label' => 'Edit Variant',
		        'url' => route('admin.products.variants.update', [$product, $variant]),
		        'primary' => true,
		        'icon' =>
		            '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\'/></svg>',
		    ],
		]"
		:links="[
					['label' => 'Overview', 'url' => route('admin.products.variants.show', [$product, $variant]), 'active' => true],
					['label' => 'Stock Movements', 'url' => route('admin.inventory.movements.index', ['variant_id' => $variant->id])],
					['label' => 'Purchase Batches', 'url' => route('admin.purchase-batches.index', ['variant_id' => $variant->id])],
					['label' => 'Orders', 'url' => route('admin.orders.index', ['variant_id' => $variant->id])],
				]"
		backUrl="{{ route('admin.products.show', $product) }}" />

	{{-- Stats Row --}}
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card title="Current Stock" :value="$variant->stock_quantity" subtitle="units available" icon="stock" :color="$variant->stock_quantity > 0 ? 'green' : 'red'"
			:url="route('admin.inventory.movements.index', ['variant_id' => $variant->id])" />
		<x-admin.ui.stat-card title="Selling Price" :value="'৳' . number_format($variant->price, 2)" :subtitle="'Cost: ৳' . number_format($variant->cost_price, 2)" icon="currency" color="blue" />
		<x-admin.ui.stat-card title="Profit per Unit" :value="'৳' . number_format($variant->price - $variant->cost_price, 2)" :subtitle="number_format((($variant->price - $variant->cost_price) / $variant->price) * 100, 1) . '% margin'" icon="profit" :color="$variant->price - $variant->cost_price > 0 ? 'green' : 'red'" />
		<x-admin.ui.stat-card title="Total Orders" value="0" subtitle="all time" icon="cart" color="purple"
			:url="route('admin.orders.index', ['variant_id' => $variant->id])" />
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Left Column - Main Content -->
		<div class="lg:col-span-2 space-y-6">
			{{-- Variant Info Card --}}
			<x-admin.ui.info-card title="Variant Information" :columns="2" :items="[
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
			]" />

			{{-- Attributes --}}
			@if ($variant->variantAttributes && $variant->variantAttributes->count() > 0)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Variant Attributes</h4>
					</div>
					<div class="p-6">
						<div class="grid grid-cols-2 gap-4">
							@foreach ($variant->variantAttributes as $attr)
								<div>
									<span class="text-sm text-gray-500 dark:text-gray-400">{{ $attr->attribute_name }}</span>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $attr->attribute_value }}</p>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			@endif

			{{-- Image --}}
			@if ($variant->image)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Variant Image</h4>
					</div>
					<div class="p-6">
						<img src="{{ asset('storage/' . $variant->image) }}" alt="{{ $variant->full_name }}"
							class="w-64 h-64 object-cover rounded-lg">
					</div>
				</div>
			@endif
		</div>

		{{-- Right Column - Sidebar --}}
		<div class="space-y-6">
			{{-- Pricing Card --}}
			<x-admin.ui.info-card title="Pricing" :items="[
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
			]" />

			{{-- Stock Card --}}
			<x-admin.ui.info-card title="Inventory" :items="[
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
			]" />

			{{-- Metadata --}}
			<x-admin.ui.info-card title="Metadata" :items="[
			    ['label' => 'Created', 'value' => $variant->created_at->format('M d, Y')],
			    ['label' => 'Updated', 'value' => $variant->updated_at->format('M d, Y')],
			]" />
		</div>
	</div>
@endsection
