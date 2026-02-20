@extends('admin.layouts.app')

@section('title', "Create Product — Kiddo's Heaven")

@section('content')
	<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form" x-data="productCreate()" x-init="initSelects()">
		@csrf

		{{-- Header --}}
		<div class="flex items-center justify-between mb-6">
			<div>
				<h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Create Product</h1>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new product to your catalog</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.products.index') }}"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					Cancel
				</a>
				<button type="submit"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
					</svg>
					Create Product
				</button>
			</div>
		</div>

		{{-- Main Grid --}}
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
			{{-- Left Column --}}
			<div class="lg:col-span-8 space-y-6">

				{{-- Step 1: Basic Information --}}
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<div class="flex items-center gap-3">
							<span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-semibold">1</span>
							<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Basic Information</h2>
						</div>
					</div>
					<div class="p-6 space-y-4">
						{{-- Product Name --}}
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Name *</label>
							<input type="text" id="name" name="name" x-model="product.name" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>

						{{-- Category + Type + Brand --}}
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category *</label>
							<select id="category_id" name="category_id" x-ref="categorySelect" required
								data-placeholder="Search or select category..."
								class="searchable-select h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<option value="">Select category</option>
								@foreach ($categories as $category)
									<option value="{{ $category->id }}">{{ $category->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Type *</label>
							<select name="product_type" x-model="product.type" required data-search="false"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<option value="simple">Simple (no variations)</option>
								<option value="variable">Variable (has variations)</option>
							</select>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Brand</label>
							<select name="brand_id" x-ref="brandSelect"
								data-placeholder="Search or select brand..."
								class="searchable-select h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								</select>
							</div>
						</div>

						{{-- SKU --}}
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">SKU</label>
								<div class="flex gap-2">
									<input type="text" id="sku" name="sku" x-model="product.sku"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									<button type="button" @click="generateSKU"
										class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
										Auto
									</button>
								</div>
							</div>
							<div>
								<label for="barcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barcode</label>
								<input type="text" id="barcode" name="barcode" x-model="product.barcode"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
							</div>
						</div>

						{{-- Description --}}
						<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Short Description</label>
						<div id="quill-short_description" class="quill-editor-container" style="min-height: 140px;"></div>
						<input type="hidden" name="short_description" id="input-short_description" value="{{ old('short_description') }}">
						<p class="text-xs text-gray-500 mt-1">Brief product overview (appears in listings)</p>
					</div>

					{{-- Features --}}
					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Features & Details</label>
						<div id="quill-description" class="quill-editor-container" style="min-height: 140px;"></div>
						<input type="hidden" name="description" id="input-description" value="{{ old('description') }}">
						<p class="text-xs text-gray-500 mt-1">Detailed features, specifications, and product information</p>
				</div>

				{{-- Step 2: Attributes & Variants (shown after category selected) --}}
				<div x-show="attributes.length > 0" x-cloak class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<div class="flex items-center gap-3 mb-2">
							<span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-semibold">2</span>
							<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Attributes & Variants</h2>
						</div>
						<p class="text-sm text-gray-500">Select attribute values and mark which ones create product variants</p>
					</div>
					<div class="p-6">
						<!-- Help Text -->
				<div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
						<div class="flex gap-3">
							<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
							</svg>
							<div class="flex-1">
								<p class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">🎯 How to Create Variants</p>
								<div class="space-y-3">
									<div class="text-xs text-blue-800 dark:text-blue-300">
										<p class="font-medium mb-1">✅ Want variants for SIZE only (not Color)?</p>
										<ol class="list-decimal list-inside space-y-0.5 ml-1">
											<li><strong>Size:</strong> Select values → Check "Use for Variants" ✓</li>
											<li><strong>Color:</strong> Select values → Leave "Use for Variants" unchecked</li>
											<li>Result: Creates variants like "Small", "Medium", "Large" (Color shown as specification)</li>
										</ol>
									</div>
									<div class="text-xs text-purple-800 dark:text-purple-300">
										<p class="font-medium mb-1">🎨 Want variants for BOTH Size and Color?</p>
										<ol class="list-decimal list-inside space-y-0.5 ml-1">
											<li>Select Size values → Check "Use for Variants" ✓</li>
											<li>Select Color values → Check "Use for Variants" ✓</li>
											<li>Result: Creates combinations like "Small/Red", "Small/Blue", "Medium/Red", etc.</li>
										</ol>
									</div>
									<div class="pt-2 border-t border-blue-200 dark:border-blue-700">
										<p class="text-xs text-gray-600 dark:text-gray-400">
											💡 <strong>Tip:</strong> Use "Select All" to quickly choose values, then deselect what you don't need
										</p>
									</div>
								</div>
								</div>
							</div>
						</div>
						<template x-for="attr in attributes" :key="attr.id">
							<div class="mb-6 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
							<!-- Attribute Header -->
							<div class="flex items-start justify-between mb-3">
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="attr.name"></h4>
										<span class="px-2 py-0.5 text-xs font-medium rounded-full"
											:class="getSelectedCount(attr.id) > 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
											x-text="getSelectedCount(attr.id) + ' of ' + getTotalCount(attr.id) + ' selected'"></span>
									</div>
									<p class="text-xs text-gray-500 mt-0.5" x-text="'Choose which ' + attr.name.toLowerCase() + ' values to use'"></p>
								</div>
								<div class="flex items-center gap-2 ml-4">
									<label class="flex items-center gap-2 cursor-pointer px-2.5 py-1.5 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
										<input type="checkbox" :checked="selectedAttributesData[attr.id]?.use_for_variant" @change="toggleVariantUsage(attr.id)"
											class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
										<span class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Use for Variants</span>
									</label>
								</div>
							</div>

							<!-- Quick Actions -->
							<div class="flex items-center gap-2 mb-3">
								<button type="button" @click="selectAllValues(attr.id)"
									class="px-2.5 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded transition">
									<svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
									</svg>
									Select All
								</button>
								<button type="button" @click="clearAllValues(attr.id)"
									class="px-2.5 py-1 text-xs font-medium text-gray-600 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded transition">
									<svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
									</svg>
									Clear All
								</button>
							</div>

							<!-- Value Checkboxes -->
							<div class="flex flex-wrap gap-2">
								<template x-for="val in attr.values" :key="val.id">
									<label class="inline-flex items-center px-3 py-1.5 rounded-md border cursor-pointer transition-all"
										:class="isValueSelected(attr.id, val.id) ? 'bg-blue-50 border-blue-300 text-blue-700 dark:bg-blue-900/30 dark:border-blue-600 dark:text-blue-300 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:border-blue-200 hover:bg-blue-50/30 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-blue-700'">
										<input type="checkbox" :value="val.id" @change="toggleValue(attr.id, val.id)" :checked="isValueSelected(attr.id, val.id)" class="sr-only">
										<!-- Checkmark icon when selected -->
										<svg x-show="isValueSelected(attr.id, val.id)" class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
										</svg>
									</template>
								</div>
							</div>
						</template>

						{{-- Generate Variants Button --}}
					<div x-show="product.type === 'variable' && variantAttributesCount > 0" class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
						<div class="flex items-center justify-between">
							<div class="flex-1">
								<div class="flex items-center gap-2 mb-1">
									<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
									</svg>
									<p class="text-sm font-semibold text-purple-900 dark:text-purple-300">
										Ready to generate variants
									</p>
								</div>
								<p class="text-xs text-purple-700 dark:text-purple-400">
									<span class="font-medium" x-text="variantAttributesCount"></span> attribute(s) selected •
									<span class="font-medium" x-text="estimatedVariants"></span> variant combination(s) will be created
								</p>
							</div>
							<button type="button" @click="generateVariants"
								class="ml-4 px-5 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 transition shadow-sm hover:shadow-md flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
								</svg>
								Generate Variants
							</button>
						</div>
						</div>
					</div>
				</div>

				{{-- Step 3: Variants Table (if variants generated) --}}
				<div x-show="variants.length > 0" x-cloak class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<div class="flex items-center justify-between">
							<div class="flex items-center gap-3">
								<span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-sm font-semibold">
									<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
									</svg>
								</span>
								<div>
									<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Variants</h2>
									<p class="text-xs text-gray-500 mt-0.5">
										<span class="font-medium text-green-600 dark:text-green-400" x-text="variants.length"></span> variants generated •
										Set prices, stock, and SKUs for each
									</p>
								</div>
							</div>
							<button type="button" @click="generateVariants"
								class="px-3 py-1.5 text-xs font-medium text-purple-600 hover:text-purple-700 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/20 rounded-lg transition">
								<svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
								</svg>
								Regenerate
							</button>
						</div>
					</div>
					<div class="p-6 overflow-x-auto">
						<table class="w-full text-sm">
							<thead>
								<tr class="border-b border-gray-200 dark:border-gray-700">
									<th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Variant</th>
									<th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">SKU</th>
									<th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Price</th>
									<th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Stock</th>
									<th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Default</th>
								</tr>
							</thead>
							<tbody>
								<template x-for="(variant, idx) in variants" :key="idx">
									<tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
										<td class="px-3 py-3">
											<div class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="variant.name"></div>
											<input type="hidden" :name="'variants[' + idx + '][attributes]'" :value="JSON.stringify(variant.attributes)">
										</td>
										<td class="px-3 py-3">
											<input type="text" :name="'variants[' + idx + '][sku]'" x-model="variant.sku"
												class="h-9 w-full rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900">
										</td>
										<td class="px-3 py-3">
											<input type="number" :name="'variants[' + idx + '][price]'" x-model="variant.price" step="0.01"
												class="h-9 w-24 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900">
										</td>
										<td class="px-3 py-3">
											<input type="number" :name="'variants[' + idx + '][stock_quantity]'" x-model="variant.stock_quantity"
												class="h-9 w-20 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900">
										</td>
										<td class="px-3 py-3">
											<input type="radio" :name="'default_variant'" :value="idx" @click="setDefaultVariant(idx)"
												class="w-4 h-4 text-blue-600 focus:ring-blue-500">
											<input type="hidden" :name="'variants[' + idx + '][is_default]'" :value="variant.is_default ? '1' : '0'">
										</td>
									</tr>
								</template>
							</tbody>
						</table>
					</div>
				</div>

			</div>

			{{-- Right Column --}}
			<div class="lg:col-span-4 space-y-6">

				{{-- Pricing (for simple products) --}}
				<div x-show="product.type === 'simple'" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Pricing</h3>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Price *</label>
							<input type="number" name="price" x-model="product.price" step="0.01" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cost Price</label>
							<input type="number" name="cost_price" x-model="product.cost_price" step="0.01"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock Quantity</label>
							<input type="number" name="stock_quantity" x-model="product.stock_quantity"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
					</div>
				</div>

				{{-- Status --}}
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Status</h3>
					</div>
					<div class="p-6 space-y-3">
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
						</label>
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Featured</span>
						</label>
					</div>
				</div>

				{{-- Images --}}
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Images</h3>
					</div>
					<div class="p-6">
						<input type="file" name="images[]" multiple accept="image/*"
							class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
						<p class="text-xs text-gray-500 mt-2">Upload product images (max 20MB each &mdash; auto-compressed to &le;1MB)</p>
					</div>
				</div>

			</div>
		</div>

		{{-- Hidden Field for Attribute Configs --}}
		<input type="hidden" name="attribute_configs" x-model="attributeConfigsJSON">
	</form>

	<script>
		function productCreate() {
			return {
				product: {
					name: '',
					category_id: '',
					type: 'simple',
					brand_id: '',
					sku: '',
					barcode: '',
					description: '',
					price: '',
					cost_price: '',
					stock_quantity: 0
				},
				attributes: [],
				variants: [],
				selectedAttributesData: {}, // { attrId: { use_for_variant: bool, selected_values: [valId1, valId2] } }

				get variantAttributesCount() {
					return Object.values(this.selectedAttributesData).filter(a => a.use_for_variant).length;
				},

				get estimatedVariants() {
					let count = 1;
					Object.values(this.selectedAttributesData).forEach(attr => {
						if (attr.use_for_variant && attr.selected_values.length > 0) {
							count *= attr.selected_values.length;
						}
					});
					return count;
				},

				get attributeConfigsJSON() {
					const configs = [];
					Object.entries(this.selectedAttributesData).forEach(([attrId, data]) => {
						if (data.selected_values.length > 0) {
							configs.push({
								attribute_id: attrId,
								usage_type: data.use_for_variant ? 'variant' : 'specification',
								values: data.selected_values
							});
						}
					});
					return JSON.stringify(configs);
				},

				async loadAttributes() {
					if (!this.product.category_id) return;

					try {
						const response = await fetch(`/admin/products/attributes/${this.product.category_id}`);
						const data = await response.json();

						if (data.success) {
							this.attributes = data.attributes || [];
							// Initialize selectedAttributesData
							this.selectedAttributesData = {};
							this.attributes.forEach(attr => {
								this.selectedAttributesData[attr.id] = {
									use_for_variant: false,
									selected_values: []
								};
							});
						}
					} catch (error) {
						console.error('Failed to load attributes:', error);
					}
				},

				toggleVariantUsage(attrId) {
					if (this.selectedAttributesData[attrId]) {
						this.selectedAttributesData[attrId].use_for_variant = !this.selectedAttributesData[attrId].use_for_variant;
					}
				},

				toggleValue(attrId, valId) {
					if (!this.selectedAttributesData[attrId]) return;

					const values = this.selectedAttributesData[attrId].selected_values;
					const index = values.indexOf(valId);

					if (index > -1) {
						values.splice(index, 1);
					} else {
						values.push(valId);
					}
				},

				isValueSelected(attrId, valId) {
					return this.selectedAttributesData[attrId]?.selected_values.includes(valId) || false;
				},

				selectAllValues(attrId) {
					if (!this.selectedAttributesData[attrId]) return;

					const attr = this.attributes.find(a => a.id === attrId);
					if (!attr) return;

					this.selectedAttributesData[attrId].selected_values = attr.values.map(v => v.id);
				},

				clearAllValues(attrId) {
					if (!this.selectedAttributesData[attrId]) return;
					this.selectedAttributesData[attrId].selected_values = [];
				},

				getSelectedCount(attrId) {
					return this.selectedAttributesData[attrId]?.selected_values.length || 0;
				},

				getTotalCount(attrId) {
					const attr = this.attributes.find(a => a.id === attrId);
					return attr?.values.length || 0;
				},

				generateVariants() {
					// Get variant attributes with their selected values
					const variantAttrs = [];
					this.attributes.forEach(attr => {
						const data = this.selectedAttributesData[attr.id];
						if (data.use_for_variant && data.selected_values.length > 0) {
							variantAttrs.push({
								id: attr.id,
								name: attr.name,
								values: attr.values.filter(v => data.selected_values.includes(v.id))
							});
						}
					});

					// Generate combinations
					const combinations = this.generateCombinations(variantAttrs);

					// Create variant objects
					this.variants = combinations.map((combo, idx) => {
						const variantName = combo.map(c => c.value).join(' - ');
						const attributes = combo.reduce((acc, c) => {
							acc[c.attrId] = c.valueId;
							return acc;
						}, {});

						return {
							name: variantName,
							sku: this.product.sku ? `${this.product.sku}-${idx + 1}` : `VAR-${idx + 1}`,
							price: this.product.price || 0,
							stock_quantity: 0,
							is_default: idx === 0,
							attributes: attributes
						};
					});
				},

				generateCombinations(attrs, current = [], index = 0) {
					if (index === attrs.length) {
						return [current];
					}

					const results = [];
					const attr = attrs[index];

					attr.values.forEach(val => {
						const combo = [...current, {
							attrId: attr.id,
							valueId: val.id,
							value: val.value
						}];
						results.push(...this.generateCombinations(attrs, combo, index + 1));
					});

					return results;
				},

				setDefaultVariant(idx) {
					this.variants.forEach((v, i) => {
						v.is_default = i === idx;
					});
				},

				generateSKU() {
					this.product.sku = 'SKU-' + Math.random().toString(36).substring(2, 8).toUpperCase();
				},

				// Initialize Select2 on mount
				initSelects() {
					// Wait for Alpine to fully initialize
					this.$nextTick(() => {
						// Initialize category select
						const categorySelect = $(this.$refs.categorySelect);
						categorySelect.select2({
							placeholder: 'Search or select category...',
							width: '100%',
							minimumResultsForSearch: 0,
							dropdownAutoWidth: true
						}).on('change', (e) => {
							this.product.category_id = e.target.value;
							this.loadAttributes();
						});

						// Initialize brand select
						const brandSelect = $(this.$refs.brandSelect);
						brandSelect.select2({
							placeholder: 'Search or select brand...',
							width: '100%',
							minimumResultsForSearch: 0,
							dropdownAutoWidth: true
						}).on('change', (e) => {
							this.product.brand_id = e.target.value;
						});
					});
				}
			}
		}
	</script>

	@push('scripts')
	<!-- Quill Rich Text Editor -->
	<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var quillFields = ['short_description', 'description'];
			var toolbarOptions = [
				['bold', 'italic', 'underline', 'strike'],
				[{'list': 'ordered'}, {'list': 'bullet'}],
				[{'indent': '-1'}, {'indent': '+1'}],
				['link', 'clean']
			];
			quillFields.forEach(function(field) {
				var container = document.getElementById('quill-' + field);
				if (!container) return;
				var q = new Quill(container, {
					theme: 'snow',
					modules: { toolbar: toolbarOptions }
				});
				var hidden = document.getElementById('input-' + field);
				if (hidden && hidden.value) q.root.innerHTML = hidden.value;
				q.on('text-change', function() { if (hidden) hidden.value = q.root.innerHTML; });
			});
			var form = document.getElementById('product-form');
			if (form) {
				form.addEventListener('submit', function() {
					quillFields.forEach(function(field) {
						var el = document.getElementById('quill-' + field);
						var hidden = document.getElementById('input-' + field);
						if (el && hidden) {
							var q = Quill.find(el);
							if (q) hidden.value = q.root.innerHTML;
						}
					});
				});
			}
		});
	</script>
@endpush

@push('styles')
	<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

	<style>
		[x-cloak] { display: none !important; }
			/* Select2 Dark Mode Styles */
		.select2-container--default .select2-selection--single {
			border-radius: 0.5rem;
			height: 44px;
			display: flex;
			align-items: center;
		}

		.dark .select2-container--default .select2-selection--single {
			background-color: rgb(17 24 39);
			border-color: rgb(55 65 81);
			color: rgba(255, 255, 255, 0.9);
		}

		.dark .select2-container--default .select2-selection--single .select2-selection__rendered {
			color: rgba(255, 255, 255, 0.9);
		}

		.dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
			color: rgba(255, 255, 255, 0.3);
		}

		.dark .select2-dropdown {
			background-color: rgb(17 24 39);
			border-color: rgb(55 65 81);
		}

		.dark .select2-container--default .select2-results__option {
			background-color: rgb(17 24 39);
			color: rgba(255, 255, 255, 0.9);
		}

		.dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
			background-color: rgb(59 130 246);
			color: white;
		}

		.dark .select2-container--default .select2-search--dropdown .select2-search__field {
			background-color: rgb(31 41 55);
			border-color: rgb(55 65 81);
			color: rgba(255, 255, 255, 0.9);
		}

		.dark .select2-container--default .select2-selection--single .select2-selection__arrow b {
			border-color: rgba(255, 255, 255, 0.5) transparent transparent transparent;
		}

		.dark .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
			border-color: transparent transparent rgba(255, 255, 255, 0.5) transparent;
		}

		/* Quill Editor Styles */
		.quill-editor-container {
			overflow: visible;
			position: relative;
		}
		/* ── Light mode ── */
		.quill-editor-container .ql-toolbar.ql-snow {
			border-radius: 0.5rem 0.5rem 0 0 !important;
			border: 1px solid #d1d5db !important;
			border-bottom: none !important;
			background-color: #f9fafb !important;
		}
		.quill-editor-container .ql-container.ql-snow {
			border-radius: 0 0 0.5rem 0.5rem !important;
			border: 1px solid #d1d5db !important;
			border-top: none !important;
			font-size: 0.875rem;
		}
		.quill-editor-container .ql-editor {
			min-height: 100px;
			color: #1f2937;
		}
		.quill-editor-container .ql-toolbar.ql-snow .ql-picker-options {
			z-index: 100;
			border-radius: 0.375rem;
		}
		/* ── Dark mode ── */
		.dark .quill-editor-container .ql-toolbar.ql-snow {
			background-color: #1f2937 !important;
			border: 1px solid #374151 !important;
			border-bottom: none !important;
		}
		.dark .quill-editor-container .ql-container.ql-snow {
			background-color: #111827 !important;
			border: 1px solid #374151 !important;
			border-top: none !important;
		}
		.dark .quill-editor-container .ql-editor {
			color: #e5e7eb;
		}
		.dark .quill-editor-container .ql-editor.ql-blank::before {
			color: rgba(255,255,255,0.3);
			font-style: italic;
		}
		.dark .quill-editor-container .ql-editor p,
		.dark .quill-editor-container .ql-editor li {
			color: #e5e7eb;
		}
		.dark .quill-editor-container .ql-toolbar .ql-stroke { stroke: #9ca3af; }
		.dark .quill-editor-container .ql-toolbar .ql-fill  { fill:   #9ca3af; }
		.dark .quill-editor-container .ql-toolbar button:hover .ql-stroke,
		.dark .quill-editor-container .ql-toolbar button.ql-active .ql-stroke { stroke: #60a5fa; }
		.dark .quill-editor-container .ql-toolbar .ql-picker-label { color: #9ca3af; }
		.dark .quill-editor-container .ql-toolbar .ql-picker-options {
			background-color: #1f2937;
			border: 1px solid #374151;
		}
		.dark .quill-editor-container .ql-toolbar .ql-picker-item { color: #d1d5db; }
	</style>
@endsection
