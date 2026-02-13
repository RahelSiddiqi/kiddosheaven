@extends('admin.layouts.app')

@section('title', "Create Product — Kiddo's Heaven")

@section('content')
	@if ($errors->any())
		<div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
			<ul class="list-disc pl-5 space-y-1">
				@foreach ($errors->all() as $error)
					<li class="text-sm text-red-700 dark:text-red-400">{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form"
		class="pb-28">
		@csrf

		<div class="flex items-center justify-between mb-6">
			<div>
				<h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Create Product</h1>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new product to your catalog</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.products.index') }}"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					Back to Products
				</a>
				<button type="submit" form="product-form"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
					Create Product
				</button>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
			<div class="lg:col-span-8 space-y-4 md:space-y-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Basic Information</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name') }}" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
							<div>
								<label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category
									*</label>
								<select id="category_id" name="category_id" required onchange="loadCategoryAttributes()"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="">Select category</option>
									@foreach ($categories as $category)
										<option value="{{ $category->id }}"
											{{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
											{{ $category->name }} ({{ $category->variant_count ?? 0 }} variants)</option>
									@endforeach
								</select>
							</div>
							<div>
								<label for="brand_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Brand</label>
								<select id="brand_id" name="brand_id"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="">Select brand</option>
									@foreach ($brands as $brand)
										<option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
										</option>
									@endforeach
								</select>
							</div>
							<div>
								<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
								<select id="status" name="status"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
									<option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
							<div>
								<label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">SKU</label>
								<div class="flex gap-2">
									<input type="text" id="sku" name="sku" value="{{ old('sku') }}" maxlength="100"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
									<button type="button" onclick="generateSkuCreate('sku')"
										class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Generate</button>
								</div>
							</div>
							<div>
								<label for="barcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barcode</label>
								<input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}" maxlength="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label for="product_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product
									Type</label>
								<select id="product_type" name="product_type" onchange="toggleVariantSection()"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="simple" {{ old('product_type', 'simple') == 'simple' ? 'selected' : '' }}>Simple</option>
									<option value="variable" {{ old('product_type') == 'variable' ? 'selected' : '' }}>Variable</option>
									<option value="digital" {{ old('product_type') == 'digital' ? 'selected' : '' }}>Digital</option>
								</select>
							</div>
						</div>
						<div>
							<label for="short_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Short
								Description</label>
							<input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}"
								maxlength="500"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
							<textarea id="description" name="description" rows="4"
							 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('description') }}</textarea>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Details</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Features</label>
							<textarea name="features" rows="3" placeholder="• Feature 1&#10;• Feature 2&#10;• Feature 3"
							 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('features') }}</textarea>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Care Instructions</label>
								<textarea name="care_instructions" rows="2" placeholder="Machine wash cold, tumble dry low"
								 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('care_instructions') }}</textarea>
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ingredients</label>
								<textarea name="ingredients" rows="2" placeholder="List of ingredients"
								 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('ingredients') }}</textarea>
							</div>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Safety Warning</label>
							<textarea name="safety_warning" rows="2" placeholder="Not suitable for children under 3 years"
							 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('safety_warning') }}</textarea>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">SEO</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
								Title</label>
							<input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div>
							<label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
								Description</label>
							<textarea id="meta_description" name="meta_description" maxlength="500" rows="2"
							 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('meta_description') }}</textarea>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Images</h2>
						<span class="text-sm text-gray-500 dark:text-gray-400">Add images to showcase the product</span>
					</div>
					<div class="p-6">
						<div class="flex flex-wrap gap-3 items-center" id="image-container">
							<div id="image-preview" class="flex flex-wrap gap-3"></div>
							<!-- Primary Image Selection (hidden initially) -->
							<div id="primary-image-section" class="mt-4" style="display:none">
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Primary Image</label>
								<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Select which image should be the main product image</p>
								<div id="primary-image-options" class="flex flex-wrap gap-2"></div>
								<input type="hidden" name="primary_image" id="primary_image" value="">
							</div>

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
							<input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
								placeholder="https://youtube.com/watch?v=..."
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
					</div>
				</div>

				<!-- Non-Variant Attributes Section -->
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
							<p class="text-sm text-gray-500 dark:text-gray-400">Choose a category and click "Load Details" to add product
								details like Material, Age Range, Gender, etc.</p>
							<div id="non-variant-attribute-fields" class="mt-4"></div>
						</div>
						<input type="hidden" id="non_variant_attributes" name="non_variant_attributes">
					</div>
				</div>

				<div id="variants-section" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3"
					style="{{ old('product_type', 'simple') === 'variable' ? '' : 'display:none' }}">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Variants</h2>
						<button type="button" onclick="loadVariantAttributes()"
							class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 text-sm rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
							<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							Load Attributes
						</button>
					</div>
					<div class="p-6 space-y-4">
						<div id="variant-attributes-container" class="mb-4">
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Select attribute values to generate variant
								combinations.</p>
							<div id="variant-attribute-checkboxes" class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
								<p class="text-sm text-gray-500">Choose a category and click "Load Attributes" to begin.</p>
							</div>
						</div>
						<div id="variants-table-container">
							<p class="text-sm text-gray-500 dark:text-gray-400">No variants yet. Select attributes and click "Generate
								Combinations".</p>
						</div>
					</div>
				</div>
			</div>

			<div class="lg:col-span-4 space-y-4 md:space-y-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pricing (BDT)</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Selling Price
								*</label>
							<input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}"
								min="0" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="discount_price"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Discount</label>
								<input type="number" step="0.01" id="discount_price" name="discount_price"
									value="{{ old('discount_price') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label for="discount_type"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
								<select id="discount_type" name="discount_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'selected' : '' }}>%
									</option>
									<option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="cost_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cost
									Price</label>
								<input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label for="vat_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">VAT %</label>
								<input type="number" step="0.01" id="vat_rate" name="vat_rate" value="{{ old('vat_rate') }}"
									min="0" max="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
						</div>
						<div>
							<label for="wholesale_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Wholesale
								Price</label>
							<input type="number" step="0.01" id="wholesale_price" name="wholesale_price"
								value="{{ old('wholesale_price') }}" min="0"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Inventory</h2>
					</div>
					<div class="p-6 space-y-4">
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
									*</label>
								<input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
									min="0" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label for="low_stock_alert" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Low
									Alert</label>
								<input type="number" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', 5) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
						</div>
						<div>
							<label for="stock_status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
								Status</label>
							<select id="stock_status" name="stock_status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
								<option value="in_stock" {{ old('stock_status', 'in_stock') == 'in_stock' ? 'selected' : '' }}>In Stock
								</option>
								<option value="out_of_stock" {{ old('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
								</option>
								<option value="pre_order" {{ old('stock_status') == 'pre_order' ? 'selected' : '' }}>Pre-Order</option>
								<option value="backorder" {{ old('stock_status') == 'backorder' ? 'selected' : '' }}>Backorder</option>
							</select>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Shipping</h2>
					</div>
					<div class="p-6 space-y-4">
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="weight" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weight
									(kg)</label>
								<input type="number" step="0.01" id="weight" name="weight" value="{{ old('weight') }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label for="delivery_type"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Delivery</label>
								<select id="delivery_type" name="delivery_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-border-blue-800">
									<option value="instant" {{ old('delivery_type', 'instant') == 'instant' ? 'selected' : '' }}>Instant</option>
									<option value="schedule" {{ old('delivery_type') == 'schedule' ? 'selected' : '' }}>Scheduled</option>
									<option value="frozen" {{ old('delivery_type') == 'frozen' ? 'selected' : '' }}>Frozen</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-3 gap-2">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">L (cm)</label>
								<input type="number" step="0.01" name="length" value="{{ old('length') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">W (cm)</label>
								<input type="number" step="0.01" name="width" value="{{ old('width') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">H (cm)</label>
								<input type="number" step="0.01" name="height" value="{{ old('height') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
							</div>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tags & Options</h2>
					</div>
					<div class="p-6 space-y-4">
						<label class="flex items-center gap-3 cursor-pointer">
							<input type="checkbox" name="is_featured" value="1" id="is_featured"
								{{ old('is_featured') ? 'checked' : '' }}
								class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Featured Product</span>
						</label>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tags</label>
							@php
								$tags = old('tags', []);
								if (empty($tags)) {
								    $tags = [''];
								}
							@endphp
							<div id="tags-container-create" class="grid gap-2">
								@foreach ($tags as $index => $tag)
									<input type="text" name="tags[]" value="{{ $tag }}" maxlength="50"
										placeholder="Tag {{ $index + 1 }}"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
								@endforeach
							</div>
							<div class="pt-2">
								<button type="button" onclick="addTagInput('tags-container-create')"
									class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
									<span class="text-base leading-none">+</span>
									<span>Add tag</span>
								</button>
							</div>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Certifications & Policies</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="return_policy" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Return
								Policy</label>
							<textarea id="return_policy" name="return_policy" rows="2"
							 class="rich-text w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus-border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark-border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">{{ old('return_policy') }}</textarea>
						</div>
						<div>
							<label for="warranty" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warranty</label>
							<input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus-border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark-border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div>
							<label for="manufacturer"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Manufacturer</label>
							<input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus-border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark-border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus-border-blue-800">
						</div>
						<div class="flex flex-col gap-3">
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="halal_certified" value="1" {{ old('halal_certified') ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Halal Certified</span>
							</label>
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="organic_certified" value="1"
									{{ old('organic_certified') ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Organic Certified</span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				// Collect non-variant attributes before form submission
				const productForm = document.getElementById('product-form');
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

				const dropArea = document.getElementById('drop-area');
				const input = document.getElementById('images');
				const container = document.getElementById('image-container');
				const preview = document.getElementById('image-preview');
				const newImageMap = new Map();

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
													<label class="absolute bottom-1 right-1 cursor-pointer bg-white/90 dark:bg-gray-800 rounded px-1">
														<input type="radio" name="primary_image_radio" value="${fileId}" class="w-3 h-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10">
													</label>
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

				window.toggleVariantSection = function() {
					const section = document.getElementById('variants-section');
					const type = document.getElementById('product_type').value;
					section.style.display = type === 'variable' ? '' : 'none';
					if (type === 'variable') {
						const categoryId = document.getElementById('category_id').value;
						if (categoryId) {
							loadVariantAttributes();
						}
					}
				};

				window.updateDefaultVariant = function() {
					document.querySelectorAll('.variant-default-hidden').forEach(h => h.value = '0');
					const checked = document.querySelector('input[name="default_variant"]:checked');
					if (checked) {
						const row = checked.closest('tr');
						const hidden = row.querySelector('.variant-default-hidden');
						if (hidden) hidden.value = '1';
					}
				};

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

						if (attr.type === 'select' || attr.type === 'multiselect') {
							html += `<select class="non-variant-attr non-variant-select w-full h-10 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" data-attr-id="${attr.id}" data-attr-name="${attr.name}">
								<option value="">Select ${attr.name}</option>`;
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

					// Initialize Choices.js for searchable selects
					setTimeout(() => {
						document.querySelectorAll('.non-variant-select').forEach(select => {
							if (!select.dataset.choicesInitialized) {
								new Choices(select, {
									searchEnabled: true,
									shouldSort: false,
									itemSelectText: ''
								});
								select.dataset.choicesInitialized = 'true';
							}
						});
					}, 100);
				}

				window.collectNonVariantAttributes = function() {
					const attributes = [];
					document.querySelectorAll('.non-variant-attr').forEach(field => {
						const attrId = field.dataset.attrId;
						const attrName = field.dataset.attrName;
						const value = field.type === 'checkbox' ? field.checked : field.value;

						if (value) {
							attributes.push({
								attribute_id: attrId,
								value: value
							});
						}
					});
					return attributes;
				};

				window.loadCategoryAttributes = function() {
					if (document.getElementById('product_type').value === 'variable') {
						loadVariantAttributes();
					}
					loadNonVariantAttributes();
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
					html +=
						`<button type="button" onclick="generateVariantsFromSelection()" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600">Generate Combinations</button>`;
					container.innerHTML = html;
				}

				window.generateVariantsFromSelection = function() {
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
						const attrHiddens = combo.map(c =>
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

				window.generateSkuCreate = function(inputId) {
					const el = document.getElementById(inputId);
					if (!el) return;
					el.value = 'SKU-' + Math.random().toString(36).substring(2, 8).toUpperCase();
				};

				window.loadCategoryAttributes = function() {
					if (document.getElementById('product_type').value === 'variable') {
						loadVariantAttributes();
					}
				};

				const statusSelect = document.getElementById('status');
				document.getElementById('save-draft-btn').addEventListener('click', () => {
					if (statusSelect) statusSelect.value = 'inactive';
					document.getElementById('product-form').submit();
				});
				document.getElementById('publish-btn').addEventListener('click', () => {
					if (statusSelect) statusSelect.value = 'active';
				});

				const enhanceSelects = () => {
					if (!window.Choices) return false;
					['category_id', 'brand_id', 'delivery_type', 'status', 'product_type', 'stock_status',
						'discount_type'
					]
					.forEach(id => {
						const el = document.getElementById(id);
						if (el && !el.dataset.enhanced) {
							try {
								new Choices(el, {
									searchEnabled: true,
									shouldSort: false,
									itemSelectText: ''
								});
								el.dataset.enhanced = 'true';
							} catch (e) {
								console.error('Failed to initialize Choices for ' + id, e);
							}
						}
					});
					return true;
				};

				const enhanceRichText = () => {
					if (!window.tinymce || !tinymce.init) return false;
					try {
						const existingEditors = (tinymce.EditorManager && tinymce.EditorManager.editors) || [];
						if (Array.isArray(existingEditors) && existingEditors.length) return true;
						const isDark = document.documentElement.classList.contains('dark');
						tinymce.init({
							selector: 'textarea.rich-text',
							menubar: false,
							height: 220,
							statusbar: false,
							plugins: 'lists link',
							toolbar: 'undo redo | bold italic underline | bullist numlist | link removeformat',
							skin: isDark ? 'oxide-dark' : 'oxide',
							content_css: isDark ? 'dark' : 'default',
							content_style: isDark ?
								'body { background-color: #0f172a; color: #e5e7eb; }' :
								'body { background-color: #ffffff; color: #111827; }'
						});
						return true;
					} catch (e) {
						console.error('Failed to initialize TinyMCE', e);
						return false;
					}
				};

				const tryEnhance = () => {
					const selectsOk = enhanceSelects();
					const editorsOk = enhanceRichText();
					if (!selectsOk || !editorsOk) {
						setTimeout(tryEnhance, 200);
					}
				};

				// Ensure scripts are loaded before trying to enhance
				const scriptLoadWait = () => {
					if (window.Choices && window.tinymce) {
						tryEnhance();
					} else {
						setTimeout(scriptLoadWait, 100);
					}
				};

				if (document.readyState === 'complete') {
					scriptLoadWait();
				} else {
					window.addEventListener('load', scriptLoadWait, {
						once: true
					});
				}

				toggleVariantSection();
			});
		</script>
	@endpush
	@push('styles')
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
		<style>
			/* Choices theme alignment with light/dark */
			.choices,
			.choices__inner,
			.choices__list--single {
				background-color: #fff;
				border-color: #d1d5db;
				color: #111827;
				min-height: 44px;
				height: 44px;
				padding: 0;
				border-radius: 12px;
				font-size: 0.875rem;
				line-height: 1.25rem;
				display: flex;
				align-items: center;
				transition: border-color 0.15s ease, box-shadow 0.15s ease;
			}

			.choices__inner {
				padding: 0 14px;
			}

			.choices__list--single {
				padding: 0;
			}

			.dark .choices__inner,
			.dark .choices__list--single {
				background-color: #111827;
				border-color: #374151;
				color: #e5e7eb;
			}

			.choices.is-focused .choices__inner {
				border-color: #93c5fd;
				box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
			}

			.dark .choices.is-focused .choices__inner {
				border-color: #60a5fa;
				box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18);
			}

			.choices__list--dropdown {
				background-color: #fff;
				border-color: #d1d5db;
				color: #111827;
				z-index: 60;
				border-radius: 12px;
				font-size: 0.875rem;
				overflow: auto;
			}

			.dark .choices__list--dropdown {
				background-color: #0b1220;
				border-color: #374151;
				color: #e5e7eb;
			}

			.choices__list--dropdown .choices__item--selectable.is-highlighted,
			.dark .choices__list--dropdown .choices__item--selectable.is-highlighted {
				background-color: #2563eb;
				color: #fff;
			}

			.choices[data-type*=select-one] .choices__input {
				background-color: transparent;
				color: inherit;
			}

			.choices__placeholder {
				color: #6b7280;
			}

			.dark .choices__placeholder {
				color: #9ca3af;
			}

			/* Scrollbar styling for dropdowns */
			.choices__list--dropdown::-webkit-scrollbar {
				width: 8px;
			}

			.choices__list--dropdown::-webkit-scrollbar-track {
				background: transparent;
			}

			.choices__list--dropdown::-webkit-scrollbar-thumb {
				background: #cbd5e1;
				border-radius: 9999px;
			}

			.dark .choices__list--dropdown::-webkit-scrollbar-thumb {
				background: #475569;
			}

			/* Textareas match input background */
			textarea.rich-text,
			textarea.rich-text:focus-visible {
				background-color: #fff;
				border-radius: 12px;
				border-color: #d1d5db;
			}

			.dark textarea.rich-text,
			.dark textarea.rich-text:focus-visible {
				background-color: #0f172a;
				border-color: #374151;
				color: #e5e7eb;
			}

			.choices__list--dropdown .choices__item--disabled {
				opacity: .6;
			}

			/* Ensure TinyMCE toolbar sits below dropdowns */
			.tox-tinymce {
				z-index: 10;
			}

			.choices {
				z-index: 40;
			}
		</style>
	@endpush
	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
	@endpush
@endsection
