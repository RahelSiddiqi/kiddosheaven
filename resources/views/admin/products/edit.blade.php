@extends('admin.layouts.app')

@section('title', 'Edit Product — Kiddo\'s Heaven')

@section('content')
	<!-- Toast Notification -->
	@if (session('success'))
		<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-70"
			style="display: none;">
			<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
		</div>
	@endif

	@if ($errors->any())
		<div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
			<ul class="list-disc pl-5 space-y-1">
				@foreach ($errors->all() as $error)
					<li class="text-sm text-red-700 dark:text-red-400">{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
		id="product-form" class="pb-28">
		@csrf
		@method('PUT')

		<!-- Header Actions -->
		<div class="flex items-center justify-between mb-6">
			<div>
				<h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Product</h1>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update product information</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.products.index') }}"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					Back to Products
				</a>
				<button type="submit" form="product-form"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
					<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					Update Product
				</button>
			</div>
		</div>

		<!-- 2-Column Grid Layout -->
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
			<!-- Left Column - Main Content (8 cols) -->
			<div class="lg:col-span-8 space-y-4 md:space-y-6">
				<!-- Basic Information -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Basic Information</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('name') border-red-500 @enderror">
							@error('name')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category
									*</label>
								<select id="category_id" name="category_id" required onchange="loadCategoryAttributes()"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('category_id') border-red-500 @enderror">
									<option value="">Select category</option>
									@foreach ($categories as $category)
										<option value="{{ $category->id }}"
											{{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
											{{ $category->name }} ({{ $category->attribute_count ?? 0 }} attributes)</option>
									@endforeach
								</select>
								@error('category_id')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
							<div>
								<label for="brand_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Brand</label>
								<select id="brand_id" name="brand_id"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('brand_id') border-red-500 @enderror">
									<option value="">Select brand</option>
									@foreach ($brands as $brand)
										<option value="{{ $brand->id }}"
											{{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
											{{ $brand->name }}</option>
									@endforeach
								</select>
								@error('brand_id')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
							<div>
								<label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">SKU</label>
								<div class="flex gap-2">
									<input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" maxlength="100"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('sku') border-red-500 @enderror">
									<button type="button" onclick="generateSkuEdit('sku')"
										class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
										Generate
									</button>
								</div>
								@error('sku')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
							<div>
								<label for="barcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barcode</label>
								<input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}"
									maxlength="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="product_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product
									Type</label>
								<select id="product_type" name="product_type" onchange="toggleVariantSection()"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="simple" {{ old('product_type', $product->product_type) == 'simple' ? 'selected' : '' }}>Simple
									</option>
									<option value="variable" {{ old('product_type', $product->product_type) == 'variable' ? 'selected' : '' }}>
										Variable</option>
									<option value="digital" {{ old('product_type', $product->product_type) == 'digital' ? 'selected' : '' }}>
										Digital</option>
								</select>
							</div>
						</div>

						<div>
							<label for="short_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Short
								Description</label>
							<input type="text" id="short_description" name="short_description"
								value="{{ old('short_description', $product->short_description) }}" maxlength="500"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('short_description') border-red-500 @enderror">
							@error('short_description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
							<div id="quill-description" class="quill-editor-container" style="min-height: 120px;"></div>
							<input type="hidden" name="description" id="input-description" value="{{ old('description', $product->description) }}">
							@error('description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Product Details -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Details</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Features</label>
							<div id="quill-features" class="quill-editor-container" style="min-height: 120px;"></div>
							<input type="hidden" name="features" id="input-features" value="{{ old('features', $product->features) }}">
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Care Instructions</label>
								<div id="quill-care_instructions" class="quill-editor-container" style="min-height: 120px;"></div>
								<input type="hidden" name="care_instructions" id="input-care_instructions" value="{{ old('care_instructions', $product->care_instructions) }}">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ingredients</label>
								<div id="quill-ingredients" class="quill-editor-container" style="min-height: 120px;"></div>
								<input type="hidden" name="ingredients" id="input-ingredients" value="{{ old('ingredients', $product->ingredients) }}">
							</div>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Safety Warning</label>
							<div id="quill-safety_warning" class="quill-editor-container" style="min-height: 120px;"></div>
							<input type="hidden" name="safety_warning" id="input-safety_warning" value="{{ old('safety_warning', $product->safety_warning) }}">
						</div>
					</div>
				</div>

				<!-- Media -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Images</h2>
						@if ($product->images && is_array($product->images) && count($product->images) > 0)
							<span class="text-sm text-gray-500 dark:text-gray-400">{{ count($product->images) }} image(s)</span>
						@endif
					</div>
					<div class="p-6">
						<div class="flex flex-wrap gap-3 items-center">
							@if ($product->images && is_array($product->images) && count($product->images) > 0)
								@foreach ($product->images as $index => $image)
									@php
										$isPrimary = old('primary_image', $product->primary_image) == $image;
									@endphp
									<div
										class="relative w-30 h-30 rounded-lg overflow-hidden border-2 transition-all duration-200 {{ $isPrimary ? 'border-blue-500' : 'border-gray-200 dark:border-gray-700' }}"
										data-existing="{{ $image }}">
										<input type="hidden" name="existing_images[]" value="{{ $image }}">
										<img src="{{ Storage::url($image) }}" alt="Product image" class="w-full h-full object-cover">
										@if ($isPrimary)
											<div class="absolute bottom-1 left-1 bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded">Primary</div>
										@endif
										<label class="absolute bottom-1 right-1 cursor-pointer bg-white/90 dark:bg-gray-800 rounded px-1"
											style="min-height: 20px; height: 20px">
											<input type="radio" name="primary_image" value="{{ $image }}" {{ $isPrimary ? 'checked' : '' }}
												style="min-height: 20px"
												class="w-3 h-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600">
										</label>
										<button type="button"
											class="w-3 h-3 p-0 bg-red-500 dark:hover:bg-gray-700 rounded-lg absolute top-1 right-1 delete-image"
											style="min-width: 22px; min-height: 22px; padding: 1px" data-image="{{ $image }}" title="Delete">
											<x-icons.delete class="w-3 h-3 text-red-600 dark:text-red-400" />
										</button>
									</div>
								@endforeach
							@endif
							<div id="image-preview" class="flex flex-wrap gap-3"></div>
							<div id="drop-area"
								class="w-40 h-30 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all flex-shrink-0 dark:border-gray-700 dark:bg-gray-800/50 dark:hover:bg-gray-800">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
								<input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
							</div>
						</div>
						<div class="mt-4">
							<label for="video_url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Video
								URL</label>
							<input type="url" id="video_url" name="video_url" value="{{ old('video_url', $product->video_url) }}"
								placeholder="https://youtube.com/watch?v=..."
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
					</div>
				</div>

				<!-- Product Details (Non-Variant Attributes) Section -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Details</h2>
						<button type="button" onclick="loadNonVariantAttributes()"
							class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800/50">
							<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							Load Details
						</button>
					</div>
					<div class="p-6">
						<div id="non-variant-attributes-container">
							<p class="text-sm text-gray-500 dark:text-gray-400">Click "Load Details" to update product details like Material,
								Age Range, Gender, etc.</p>
							<div id="non-variant-attribute-fields" class="mt-4"></div>
						</div>
						<input type="hidden" id="non_variant_attributes" name="non_variant_attributes">
					</div>
				</div>

				<!-- Variants Section (only for variable products) -->
				<div id="variants-section" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3"
					style="{{ old('product_type', $product->product_type) !== 'variable' ? 'display:none' : '' }}">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Variants</h2>
						<button type="button" onclick="loadVariantAttributes()"
							class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 text-sm rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
							<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							Generate Variants
						</button>
					</div>
					<div class="p-6">
						<!-- Variant Attribute Selection -->
						<div id="variant-attributes-container" class="mb-4">
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Select attribute values to generate variant
								combinations.</p>
							<div id="variant-attribute-checkboxes" class="space-y-4">
								<!-- Loaded dynamically -->
							</div>
						</div>

						<!-- Existing Variants Table -->
						<div id="variants-table-container">
							@if ($product->variants && $product->variants->count() > 0)
								<div class="overflow-x-auto">
									<table class="w-full text-sm" id="variants-table">
										<thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
											<tr>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Variant</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">SKU</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Price (৳)</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Cost (৳)</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Stock</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Default</th>
												<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Active</th>
											</tr>
										</thead>
										<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
											@foreach ($product->variants as $index => $variant)
												<tr>
													<td class="px-3 py-2">
														<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $variant->full_name }}</span>
														<input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
														@foreach ($variant->variantAttributes as $va)
															<input type="hidden"
																name="variants[{{ $index }}][attributes][{{ $va->product_attribute_id }}]"
																value="{{ $va->product_attribute_value_id }}">
														@endforeach
													</td>
													<td class="px-3 py-2">
														<input type="text" name="variants[{{ $index }}][sku]"
															value="{{ old("variants.{$index}.sku", $variant->sku) }}"
															class="h-9 w-full rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
													</td>
													<td class="px-3 py-2">
														<input type="number" name="variants[{{ $index }}][price]"
															value="{{ old("variants.{$index}.price", $variant->price) }}" step="0.01"
															class="h-9 w-24 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
													</td>
													<td class="px-3 py-2">
														<input type="number" name="variants[{{ $index }}][cost_price]"
															value="{{ old("variants.{$index}.cost_price", $variant->cost_price) }}" step="0.01"
															class="h-9 w-24 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
													</td>
													<td class="px-3 py-2">
														<input type="number" name="variants[{{ $index }}][stock_quantity]"
															value="{{ old("variants.{$index}.stock_quantity", $variant->stock_quantity) }}"
															class="h-9 w-20 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
													</td>
													<td class="px-3 py-2 text-center">
														<input type="radio" name="default_variant" value="{{ $index }}"
															{{ $variant->is_default ? 'checked' : '' }}
															class="w-4 h-4 border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600"
															onchange="updateDefaultVariant()">
														<input type="hidden" name="variants[{{ $index }}][is_default]"
															value="{{ $variant->is_default ? '1' : '0' }}" class="variant-default-hidden">
													</td>
													<td class="px-3 py-2 text-center">
														<input type="checkbox" name="variants[{{ $index }}][is_active]" value="1"
															{{ $variant->is_active ? 'checked' : '' }}
															class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600">
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<p class="text-sm text-gray-500 dark:text-gray-400">No variants yet. Select a category with variant attributes,
									then click "Generate Variants".</p>
							@endif
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">SEO</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta Title</label>
							<input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="255"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							@error('meta_title')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta Description</label>
							<textarea id="meta_description" name="meta_description" maxlength="500" rows="2"
							 class="no-tinymce w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('meta_description', $product->meta_description) }}</textarea>
							@error('meta_description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Right Column - Sidebar (4 cols) -->
			<div class="lg:col-span-4 space-y-4 md:space-y-6">
				<!-- Status -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Status</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
							<select id="status" name="status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
								<option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive
								</option>
							</select>
						</div>
					</div>
				</div>

				<!-- Pricing -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pricing (BDT)</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Selling Price
								*</label>
							<input type="number" step="0.01" id="price" name="price"
								value="{{ old('price', $product->price) }}" min="0" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('price') border-red-500 @enderror">
							@error('price')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="discount_price"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Discount</label>
								<input type="number" step="0.01" id="discount_price" name="discount_price"
									value="{{ old('discount_price', $product->discount_price) }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="discount_type"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
								<select id="discount_type" name="discount_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="percentage"
										{{ old('discount_type', $product->discount_type) == 'percentage' ? 'selected' : '' }}>%</option>
									<option value="fixed" {{ old('discount_type', $product->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed
									</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="cost_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cost
									Price</label>
								<input type="number" step="0.01" id="cost_price" name="cost_price"
									value="{{ old('cost_price', $product->cost_price) }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('cost_price') border-red-500 @enderror">
							</div>
							<div>
								<label for="vat_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">VAT %</label>
								<input type="number" step="0.01" id="vat_rate" name="vat_rate"
									value="{{ old('vat_rate', $product->vat_rate) }}" min="0" max="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
						<div>
							<label for="wholesale_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Wholesale
								Price</label>
							<input type="number" step="0.01" id="wholesale_price" name="wholesale_price"
								value="{{ old('wholesale_price', $product->wholesale_price) }}" min="0"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
					</div>
				</div>

				<!-- Inventory -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Inventory</h2>
					</div>
					<div class="p-6 space-y-4">
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
									*</label>
								<input type="number" id="stock_quantity" name="stock_quantity"
									value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('stock_quantity') border-red-500 @enderror">
								@error('stock_quantity')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
							<div>
								<label for="low_stock_alert" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Low
									Alert</label>
								<input type="number" id="low_stock_alert" name="low_stock_alert"
									value="{{ old('low_stock_alert', $product->low_stock_alert) }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
						<div>
							<label for="stock_status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
								Status</label>
							<select id="stock_status" name="stock_status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="in_stock" {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>In
									Stock</option>
								<option value="out_of_stock"
									{{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
								<option value="pre_order" {{ old('stock_status', $product->stock_status) == 'pre_order' ? 'selected' : '' }}>
									Pre-Order</option>
								<option value="backorder" {{ old('stock_status', $product->stock_status) == 'backorder' ? 'selected' : '' }}>
									Backorder</option>
							</select>
						</div>
					</div>
				</div>

				<!-- Shipping -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Shipping</h2>
					</div>
					<div class="p-6 space-y-4">
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="weight" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weight
									(kg)</label>
								<input type="number" step="0.01" id="weight" name="weight"
									value="{{ old('weight', $product->weight) }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="delivery_type"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Delivery</label>
								<select id="delivery_type" name="delivery_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="instant" {{ old('delivery_type', $product->delivery_type) == 'instant' ? 'selected' : '' }}>
										Instant</option>
									<option value="schedule" {{ old('delivery_type', $product->delivery_type) == 'schedule' ? 'selected' : '' }}>
										Scheduled</option>
									<option value="frozen" {{ old('delivery_type', $product->delivery_type) == 'frozen' ? 'selected' : '' }}>
										Frozen</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-3 gap-2">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">L (cm)</label>
								<input type="number" step="0.01" name="length" value="{{ old('length', $product->length) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">W (cm)</label>
								<input type="number" step="0.01" name="width" value="{{ old('width', $product->width) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">H (cm)</label>
								<input type="number" step="0.01" name="height" value="{{ old('height', $product->height) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
					</div>
				</div>

				<!-- Tags & Options -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tags & Options</h2>
					</div>
					<div class="p-6 space-y-4">
						<label class="flex items-center gap-3 cursor-pointer">
							<input type="checkbox" name="is_featured" value="1" id="is_featured"
								{{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
								class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Featured Product</span>
						</label>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tags</label>
							@php
								$tags = old(
								    'tags',
								    (is_string($product->tags) ? json_decode($product->tags, true) ?? [] : $product->tags) ?? [],
								);
								if (empty($tags)) {
								    $tags = [''];
								}
							@endphp
							<div id="tags-container-edit" class="grid gap-2">
								@foreach ($tags as $index => $tag)
									<input type="text" name="tags[]" value="{{ $tag }}" maxlength="50"
										placeholder="Tag {{ $index + 1 }}"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
								@endforeach
							</div>
							<div class="pt-2">
								<button type="button" onclick="addTagInput('tags-container-edit')"
									class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
									<span class="text-base leading-none">+</span>
									<span>Add tag</span>
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Certifications & Policies -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Certifications & Policies</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="return_policy" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Return
								Policy</label>
							<div id="quill-return_policy" class="quill-editor-container" style="min-height: 120px;"></div>
							<input type="hidden" name="return_policy" id="input-return_policy" value="{{ old('return_policy', $product->return_policy) }}">
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warranty</label>
							<div id="quill-warranty" class="quill-editor-container" style="min-height: 80px;"></div>
							<input type="hidden" name="warranty" id="input-warranty" value="{{ old('warranty', $product->warranty) }}">
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Manufacturer</label>
							<div id="quill-manufacturer" class="quill-editor-container" style="min-height: 80px;"></div>
							<input type="hidden" name="manufacturer" id="input-manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}">
						</div>
						<div class="flex flex-col gap-3">
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="halal_certified" value="1" id="halal_certified"
									{{ old('halal_certified', $product->halal_certified) ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Halal Certified</span>
							</label>
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="organic_certified" value="1" id="organic_certified"
									{{ old('organic_certified', $product->organic_certified) ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Organic Certified</span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div
				class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur dark:border-gray-800 dark:bg-gray-900/95">
				<div
					class="mx-auto max-w-(--breakpoint-2xl) px-4 md:px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
					<div class="flex flex-wrap gap-2">
						<a href="{{ route('admin.products.index') }}"
							class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
							Cancel
						</a>
						<button type="submit" id="update-btn"
							class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
							Update Product
						</button>
					</div>
				</div>
			</div>
	</form>

@endsection

@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				// Make selects searchable
				document.querySelectorAll('select:not(.no-select2)').forEach(function(select) {
					select.style.display = 'block';
					select.style.opacity = '0';
					select.style.position = 'absolute';
					select.style.zIndex = '-1';

					// Create custom container
					var container = document.createElement('div');
					container.className = 'custom-select relative';
					container.style.height = '44px';
					select.parentNode.insertBefore(container, select);
					container.appendChild(select);

					// Create custom display
					var display = document.createElement('div');
					display.className =
						'custom-select-display w-full h-11 px-4 flex items-center justify-between rounded-xl border border-gray-300 bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-white cursor-pointer';
					display.innerHTML = '<span class="selected-text">' + (select.options[select.selectedIndex]
							?.text || 'Select an option') +
						'</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
					container.appendChild(display);

					// Create dropdown
					var dropdown = document.createElement('div');
					dropdown.className =
						'custom-select-dropdown absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl shadow-lg hidden z-50 max-h-60 overflow-auto';
					container.appendChild(dropdown);

					// Create search input
					var search = document.createElement('input');
					search.type = 'text';
					search.className =
						'w-full px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-transparent dark:text-white focus:outline-none';
					search.placeholder = 'Search...';
					dropdown.appendChild(search);

					// Add options
					Array.from(select.options).forEach(function(option) {
						var item = document.createElement('div');
						item.className =
							'px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white';
						item.textContent = option.text;
						item.dataset.value = option.value;
						dropdown.appendChild(item);
					});

					// Toggle dropdown
					display.addEventListener('click', function(e) {
						e.stopPropagation();
						dropdown.classList.toggle('hidden');
						if (!dropdown.classList.contains('hidden')) {
							search.focus();
						}
					});

					// Select option
					dropdown.querySelectorAll(':not(input)').forEach(function(item) {
						item.addEventListener('click', function(e) {
							if (this.tagName === 'DIV' && !this.querySelector('input')) {
								select.value = this.dataset.value;
								display.querySelector('.selected-text').textContent = this
									.textContent;
								dropdown.classList.add('hidden');
								select.dispatchEvent(new Event('change'));
							}
						});
					});

					// Search
					search.addEventListener('input', function() {
						var term = this.value.toLowerCase();
						Array.from(dropdown.children).forEach(function(item) {
							if (item.tagName === 'DIV' && !item.querySelector('input')) {
								item.style.display = item.textContent.toLowerCase().includes(
									term) ? 'block' : 'none';
							}
						});
					});

					// Close on outside click
					document.addEventListener('click', function() {
						dropdown.classList.add('hidden');
					});
				});

				// Form submission
				const productForm = document.getElementById('product-form');
				if (productForm) {
					productForm.addEventListener('submit', function(e) {
						const nonVariantAttrs = collectNonVariantAttributes();
						document.getElementById('non_variant_attributes').value = JSON.stringify(nonVariantAttrs);
					});
				}
				productForm.addEventListener('submit', function(e) {
					const nonVariantAttrs = collectNonVariantAttributes();
					document.getElementById('non_variant_attributes').value = JSON.stringify(nonVariantAttrs);
				});

				window.addTagInput = function(containerId) {
					const container = document.getElementById(containerId);
					if (!container) return;
					const count = container.querySelectorAll('input[name="tags[]"]').length + 1;
					const input = document.createElement('input');
					input.type = 'text';
					input.name = 'tags[]';
					input.maxLength = 50;
					input.placeholder = `Tag ${count}`;
					input.className =
						'h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800';
					container.appendChild(input);
					input.focus();
				};

				// ========== Image Upload ==========
				const dropArea = document.getElementById('drop-area');
				const input = document.getElementById('images');
				const preview = document.getElementById('image-preview');
				const container = dropArea.parentElement;
				const newImageMap = new Map();

				document.querySelectorAll('.delete-image').forEach(btn => {
					btn.addEventListener('click', function() {
						const imagePath = this.dataset.image;
						const delInput = document.createElement('input');
						delInput.type = 'hidden';
						delInput.name = 'delete_image[]';
						delInput.value = imagePath;
						document.getElementById('product-form').appendChild(delInput);
						this.closest('[data-existing]').remove();
					});
				});

				dropArea.addEventListener('click', () => input.click());
				dropArea.addEventListener('dragover', function(e) {
					e.preventDefault();
					dropArea.classList.add('border-blue-500', 'bg-blue-50/50');
				});
				dropArea.addEventListener('dragleave', function(e) {
					e.preventDefault();
					dropArea.classList.remove('border-blue-500', 'bg-blue-50/50');
				});
				dropArea.addEventListener('drop', function(e) {
					e.preventDefault();
					dropArea.classList.remove('border-blue-500', 'bg-blue-50/50');
					if (e.dataTransfer.files.length > 0) handleFiles(e.dataTransfer.files);
				});
				input.addEventListener('change', function() {
					if (this.files.length > 0) handleFiles(this.files);
				});

				function handleFiles(files) {
					Array.from(files).forEach(file => {
						if (!file.type.startsWith('image/')) return;
						const fileId = Date.now().toString(36) + Math.random().toString(36).substr(2);
						newImageMap.set(fileId, file);
						const reader = new FileReader();
						reader.onload = function(e) {
							const div = document.createElement('div');
							div.className =
								'relative w-30 h-30 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700';
							div.dataset.fileId = fileId;
							div.innerHTML = `
								<img src="${e.target.result}" class="w-full h-full object-cover">
								<div class="absolute bottom-1 left-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded">New</div>
								<button type="button" class="remove-preview absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700" data-id="${fileId}">×</button>
							`;
							div.querySelector('.remove-preview').addEventListener('click', function() {
								newImageMap.delete(this.dataset.id);
								div.remove();
								updateFileInput();
							});
							container.insertBefore(div, dropArea);
						};
						reader.readAsDataURL(file);
					});
					updateFileInput();
				}

				function updateFileInput() {
					const dataTransfer = new DataTransfer();
					newImageMap.forEach(file => dataTransfer.items.add(file));
					input.files = dataTransfer.files;
				}

				// ========== Variant Section Toggle ==========
				window.toggleVariantSection = function() {
					const section = document.getElementById('variants-section');
					const type = document.getElementById('product_type').value;
					section.style.display = type === 'variable' ? '' : 'none';
				};

				// ========== Default Variant Radio ==========
				window.updateDefaultVariant = function() {
					document.querySelectorAll('.variant-default-hidden').forEach(h => h.value = '0');
					const checked = document.querySelector('input[name="default_variant"]:checked');
					if (checked) {
						const row = checked.closest('tr');
						const hidden = row.querySelector('.variant-default-hidden');
						if (hidden) hidden.value = '1';
					}
				};

				// ========== Load Variant Attributes (AJAX) ==========
				window.loadVariantAttributes = function() {
					const categoryId = document.getElementById('category_id').value;
					if (!categoryId) {
						alert('Please select a category first');
						return;
					}

					fetch(`/admin/products/variant-attributes/${categoryId}`)
						.then(r => r.json())
						.then(data => {
							if (data.success && data.attributes.length > 0) {
								renderVariantAttributeCheckboxes(data.attributes);
							} else {
								document.getElementById('variant-attribute-checkboxes').innerHTML =
									'<p class="text-sm text-orange-500">No variant attributes found for this category. Add attributes with "Use for Variants" enabled in the category settings.</p>';
							}
						})
						.catch(err => {
							console.error('Failed to load attributes:', err);
							alert('Failed to load variant attributes');
						});
				};

				// ========== Load Non-Variant Attributes (AJAX) ==========
				window.loadNonVariantAttributes = function() {
					const categoryId = document.getElementById('category_id').value;
					if (!categoryId) {
						alert('Please select a category first');
						return;
					}

					fetch(`/admin/products/non-variant-attributes/${categoryId}`)
						.then(r => r.json())
						.then(data => {
							if (data.success && data.attributes.length > 0) {
								renderNonVariantAttributeFields(data.attributes);
							} else {
								document.getElementById('non-variant-attribute-fields').innerHTML =
									'<p class="text-sm text-orange-500">No product details available for this category.</p>';
							}
						})
						.catch(err => {
							console.error('Failed to load attributes:', err);
							alert('Failed to load product details');
						});
				};

				function renderNonVariantAttributeFields(attributes) {
					const container = document.getElementById('non-variant-attribute-fields');
					let html = '<div class="grid grid-cols-3 gap-4">';
					attributes.forEach(attr => {
						html += `<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${attr.name}</label>`;

						if (attr.type === 'select') {
							html += `<select class="non-variant-attr non-variant-select w-full h-10 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" data-attr-id="${attr.id}" data-attr-name="${attr.name}">
								<option value="">Select ${attr.name}</option>`;
							(attr.values || []).forEach(val => {
								html += `<option value="${val.id}">${val.value}</option>`;
							});
							html += `</select>`;
						} else if (attr.type === 'multiselect') {
							html += `<select multiple class="non-variant-attr non-variant-select w-full h-32 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" data-attr-id="${attr.id}" data-attr-name="${attr.name}">
								<option value="">Select ${attr.name} (Hold Ctrl/Cmd to select multiple)</option>`;
							(attr.values || []).forEach(val => {
								html += `<option value="${val.id}">${val.value}</option>`;
							});
							html += `</select>`;
						} else if (attr.type === 'text') {
							html +=
								`<input type="text" class="non-variant-attr w-full h-10 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" data-attr-id="${attr.id}" data-attr-name="${attr.name}" placeholder="Enter ${attr.name}">`;
						} else if (attr.type === 'number') {
							html +=
								`<input type="number" class="non-variant-attr w-full h-10 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" data-attr-id="${attr.id}" data-attr-name="${attr.name}" placeholder="Enter ${attr.name}">`;
						} else if (attr.type === 'boolean') {
							html +=
								`<input type="checkbox" class="non-variant-attr w-4 h-4 rounded border-gray-300 text-blue-600" data-attr-id="${attr.id}" data-attr-name="${attr.name}">`;
						}
						html += `</div>`;
					});
					html += '</div>';
					container.innerHTML = html;

					// Initialize Select2 for searchable selects
					setTimeout(() => {
						initSelect2();
					}, 100);
				}

				window.collectNonVariantAttributes = function() {
					const attributes = [];
					document.querySelectorAll('.non-variant-attr').forEach(field => {
						const attrId = field.dataset.attrId;
						const attrName = field.dataset.attrName;

						if (field.type === 'checkbox') {
							const value = field.checked;
							if (value) {
								attributes.push({
									attribute_id: attrId,
									value: value
								});
							}
						} else if (field.multiple) {
							// Handle multiselect
							const selectedValues = Array.from(field.selectedOptions).map(opt => opt.value)
								.filter(v => v);
							if (selectedValues.length > 0) {
								attributes.push({
									attribute_id: attrId,
									value: selectedValues.join(',')
								});
							}
						} else {
							const value = field.value;
							if (value) {
								attributes.push({
									attribute_id: attrId,
									value: value
								});
							}
						}
					});
					return attributes;
				};

				function renderVariantAttributeCheckboxes(attributes) {
					const container = document.getElementById('variant-attribute-checkboxes');
					let html = '';
					attributes.forEach(attr => {
						html += `<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
							<h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${attr.name}</h4>
							<div class="flex flex-wrap gap-2">`;
						(attr.values || []).forEach(val => {
							html += `<label class="flex items-center gap-1.5 cursor-pointer px-2 py-1 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
								<input type="checkbox" class="variant-attr-check w-4 h-4 rounded border-gray-300 text-blue-600" data-attr-id="${attr.id}" data-attr-name="${attr.name}" data-val-id="${val.id}" data-val-name="${val.value}">
								<span class="text-sm text-gray-700 dark:text-gray-400">${val.value}</span>
							</label>`;
						});
						html += `</div></div>`;
					});
					html += `<button type="button" onclick="generateVariantsFromSelection()" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600">
						Generate Combinations
					</button>`;
					container.innerHTML = html;
				}

				window.generateVariantsFromSelection = function() {
					// Collect selected attributes grouped by attribute ID
					const selected = {};
					document.querySelectorAll('.variant-attr-check:checked').forEach(cb => {
						const attrId = cb.dataset.attrId;
						const attrName = cb.dataset.attrName;
						const valId = cb.dataset.valId;
						const valName = cb.dataset.valName;
						if (!selected[attrId]) selected[attrId] = {
							name: attrName,
							values: []
						};
						selected[attrId].values.push({
							id: valId,
							name: valName
						});
					});

					const attrGroups = Object.entries(selected);
					if (attrGroups.length === 0) {
						alert('Please select at least one attribute value');
						return;
					}

					// Generate all combinations
					const combinations = generateCombinations(attrGroups);
					renderVariantTable(combinations);
				};

				function generateCombinations(attrGroups, index = 0, current = []) {
					if (index === attrGroups.length) return [current.slice()];
					const [attrId, {
						name,
						values
					}] = attrGroups[index];
					const results = [];
					for (const val of values) {
						current.push({
							attrId,
							attrName: name,
							valId: val.id,
							valName: val.name
						});
						results.push(...generateCombinations(attrGroups, index + 1, current));
						current.pop();
					}
					return results;
				}

				function renderVariantTable(combinations) {
					const basePrice = parseFloat(document.getElementById('price').value) || 0;
					const baseCost = parseFloat(document.getElementById('cost_price').value) || 0;
					const baseSku = document.getElementById('sku').value || '';

					let html = `<div class="overflow-x-auto"><table class="w-full text-sm" id="variants-table">
						<thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
							<tr>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Variant</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">SKU</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Price (৳)</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Cost (৳)</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Stock</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Default</th>
								<th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Active</th>
							</tr>
						</thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">`;

					combinations.forEach((combo, index) => {
						const variantName = combo.map(c => c.valName).join(' / ');
						const sku = baseSku ? baseSku + '-' + combo.map(c => c.valName.replace(/\s+/g, '')
							.substring(0, 3).toUpperCase()).join('-') : '';
						let attrHiddens = combo.map(c =>
							`<input type="hidden" name="variants[${index}][attributes][${c.attrId}]" value="${c.valId}">`
						).join('');

						html += `<tr>
							<td class="px-3 py-2">
								<span class="text-sm font-medium text-gray-800 dark:text-white/90">${variantName}</span>
								${attrHiddens}
							</td>
							<td class="px-3 py-2"><input type="text" name="variants[${index}][sku]" value="${sku}" class="h-9 w-full rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></td>
							<td class="px-3 py-2"><input type="number" name="variants[${index}][price]" value="${basePrice.toFixed(2)}" step="0.01" class="h-9 w-24 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></td>
							<td class="px-3 py-2"><input type="number" name="variants[${index}][cost_price]" value="${baseCost.toFixed(2)}" step="0.01" class="h-9 w-24 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></td>
							<td class="px-3 py-2"><input type="number" name="variants[${index}][stock_quantity]" value="0" class="h-9 w-20 rounded border border-gray-300 bg-transparent px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></td>
							<td class="px-3 py-2 text-center">
								<input type="radio" name="default_variant" value="${index}" ${index === 0 ? 'checked' : ''} class="w-4 h-4 border-gray-300 text-blue-600" onchange="updateDefaultVariant()">
								<input type="hidden" name="variants[${index}][is_default]" value="${index === 0 ? '1' : '0'}" class="variant-default-hidden">
							</td>
							<td class="px-3 py-2 text-center">
								<input type="checkbox" name="variants[${index}][is_active]" value="1" checked class="w-4 h-4 rounded border-gray-300 text-blue-600">
							</td>
						</tr>`;
					});

					html += `</tbody></table></div>`;
					document.getElementById('variants-table-container').innerHTML = html;
				}

				// SKU generator for quick entry
				window.generateSkuEdit = function(inputId) {
					const el = document.getElementById(inputId);
					if (!el) return;
					el.value = 'SKU-' + Math.random().toString(36).substring(2, 8).toUpperCase();
				};

				// ========== Load Category Attributes (Dynamic) ==========
				window.loadCategoryAttributes = function() {
					// Also refresh variant attributes if product type is variable
					if (document.getElementById('product_type').value === 'variable') {
						loadVariantAttributes();
					}
				};

});
	</script>

	<!-- Quill Rich Text Editor -->
	<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var quillFields = ['description', 'features', 'care_instructions', 'ingredients', 'safety_warning', 'return_policy', 'warranty', 'manufacturer'];
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
			// Also sync on form submit
			var form = document.querySelector('form');
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
	<style>
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
		.dark .quill-editor-container .ql-editor li,
		.dark .quill-editor-container .ql-editor h1,
		.dark .quill-editor-container .ql-editor h2,
		.dark .quill-editor-container .ql-editor h3 {
			color: #e5e7eb;
		}
		.dark .quill-editor-container .ql-toolbar .ql-stroke { stroke: #9ca3af; }
		.dark .quill-editor-container .ql-toolbar .ql-fill  { fill:   #9ca3af; }
		.dark .quill-editor-container .ql-toolbar button:hover .ql-stroke,
		.dark .quill-editor-container .ql-toolbar button.ql-active .ql-stroke { stroke: #60a5fa; }
		.dark .quill-editor-container .ql-toolbar .ql-picker-label { color: #9ca3af; }
		.dark .quill-editor-container .ql-toolbar .ql-picker-label::before { color: #9ca3af; }
		.dark .quill-editor-container .ql-toolbar .ql-picker-options {
			background-color: #1f2937;
			border: 1px solid #374151;
		}
		.dark .quill-editor-container .ql-toolbar .ql-picker-item { color: #d1d5db; }
	</style>
@endpush
