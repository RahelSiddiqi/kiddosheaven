@extends('admin.layouts.app')

@section('title', 'Create Product — Kiddo\'s Heaven')

@push('styles')
	<style>
		/* Toast animation */
		@keyframes slideIn {
			from {
				transform: translateX(100%);
				opacity: 0;
			}

			to {
				transform: translateX(0);
				opacity: 1;
			}
		}

		[x-cloak] {
			display: none !important;
		}
	</style>
@endpush

@section('content')
	<!-- Toast Notification -->
	@if (session('success'))
		<div x-data="{ show: true }" x-show="show" x-transition
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-70"
			style="animation: slideIn 0.3s ease-out;">
			<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<span class="text-sm font-medium">{{ session('success') }}</span>
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

	<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
		@csrf

		<!-- Header Actions -->
		<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
			<div>
				<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Create Product</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new product to your catalog</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.products.index') }}"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
					</svg>
					Back
				</a>
				<button type="submit" form="product-form"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
					<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
					</svg>
					Create Product
				</button>
			</div>
		</div>

		<!-- 2-Column Grid Layout -->
		<div class="grid grid-cols-12 gap-4 md:gap-6">
			<!-- Left Column - Main Content (8 cols) -->
			<div class="col-span-12 lg:col-span-8 space-y-4 md:space-y-6">
				<!-- 1. Basic Information -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Basic Information</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Product identity card</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name') }}" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('name') border-red-500 @enderror">
							@error('name')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="catalog_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category
									*</label>
								<select id="catalog_id" name="catalog_id" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('catalog_id') border-red-500 @enderror"
									onchange="fetchAttributes(this.value); updateProductTypeInfo();">
									<option value="">Select category</option>
									@foreach ($catalogs as $catalog)
										<option value="{{ $catalog->id }}" {{ old('catalog_id') == $catalog->id ? 'selected' : '' }}>
											{{ $catalog->name }}</option>
									@endforeach
								</select>
								@error('catalog_id')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
							<div>
								<label for="brand_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Brand</label>
								<select id="brand_id" name="brand_id"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('brand_id') border-red-500 @enderror">
									<option value="">Select brand</option>
									@foreach ($brands as $brand)
										<option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
								<input type="text" id="sku" name="sku" value="{{ old('sku') }}" maxlength="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="barcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barcode</label>
								<input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}" maxlength="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Optional">
							</div>
							<div>
								<label for="product_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product
									Type</label>
								<select id="product_type" name="product_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
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
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>

						<div>
							<label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Long
								Description</label>
							<textarea id="description" name="description" rows="4"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('description') }}</textarea>
						</div>
					</div>
				</div>

				<!-- 5. Attributes System -->
				<div id="dynamic-attributes-section"
					class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
					style="display: none;">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Attributes (Category Wise)</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dynamic fields based on category</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4" id="dynamic-attributes-container">
						<!-- Attributes loaded dynamically -->
					</div>
				</div>

				<!-- 6. Variants Engine -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Variants Engine</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create variants from attributes (e.g., Size + Color)</p>
					</div>
					<div class="p-4 sm:p-6">
						<div id="variants-section">
							<p class="text-gray-500 dark:text-gray-400 text-sm">Select "Variable" product type and configure attributes to
								enable variants.</p>
						</div>
					</div>
				</div>

				<!-- 7. Specifications / Details -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Specifications & Details</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Features, care instructions, safety warnings</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Features</label>
							<textarea name="features" rows="3" placeholder="Product features (one per line)"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('features') }}</textarea>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Care Instructions
									(Clothes)</label>
								<textarea name="care_instructions" rows="2" placeholder="Washing, ironing, etc."
								 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('care_instructions') }}</textarea>
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ingredients (Food)</label>
								<textarea name="ingredients" rows="2" placeholder="Product ingredients"
								 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('ingredients') }}</textarea>
							</div>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Safety Warning (Toys)</label>
							<textarea name="safety_warning" rows="2" placeholder="Age warnings, choking hazards, etc."
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('safety_warning') }}</textarea>
						</div>
					</div>
				</div>

				<!-- 4. Media Management -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Media Management</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Images, videos, 360° view</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Images</label>
							<div class="flex flex-wrap gap-3 items-center">
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
						</div>
						<div>
							<label for="video_url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Product Video
								URL</label>
							<input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
								placeholder="YouTube or Vimeo URL"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
					</div>
				</div>

				<!-- 8. SEO Block -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">SEO</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Meta information for search engines</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
									Title</label>
								<input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
									Description</label>
								<textarea id="meta_description" name="meta_description" maxlength="500" rows="2"
								 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('meta_description') }}</textarea>
							</div>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">URL
									Slug</label>
								<input type="text" id="slug" name="slug" value="{{ old('slug') }}" maxlength="255"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="keywords"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Keywords</label>
								<input type="text" id="keywords" name="keywords" value="{{ old('keywords') }}"
									placeholder="keyword1, keyword2, keyword3"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Right Column - Sidebar (4 cols) -->
			<div class="col-span-12 lg:col-span-4 space-y-4 md:space-y-6">
				<!-- 2. Pricing Module -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Pricing</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Applies to all product types</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div>
							<label for="price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Regular Price
								(BDT) *</label>
							<input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}"
								min="1" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="discount_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sale
									Price</label>
								<input type="number" step="0.01" id="discount_price" name="discount_price"
									value="{{ old('discount_price') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="discount_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Discount
									Type</label>
								<select id="discount_type" name="discount_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'selected' : '' }}>
										Percentage</option>
									<option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
								</select>
							</div>
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="cost_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cost Price
									(BDT)</label>
								<input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', 0) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="vat_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">VAT/Tax
									(%)</label>
								<input type="number" step="0.01" id="vat_rate" name="vat_rate" value="{{ old('vat_rate', 0) }}"
									min="0" max="100"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
						<div>
							<label for="wholesale_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Wholesale
								Price (BDT)</label>
							<input type="number" step="0.01" id="wholesale_price" name="wholesale_price"
								value="{{ old('wholesale_price') }}" min="0"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
								placeholder="Optional">
						</div>
					</div>
				</div>

				<!-- 3. Inventory & Shipping -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Inventory & Shipping</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Essential for grocery & food</p>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
									Quantity *</label>
								<input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
									min="0" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="low_stock_alert" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Low
									Stock Alert</label>
								<input type="number" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', 5) }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
						<div>
							<label for="stock_status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
								Status</label>
							<select id="stock_status" name="stock_status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="in_stock" {{ old('stock_status', 'in_stock') == 'in_stock' ? 'selected' : '' }}>In Stock
								</option>
								<option value="out_of_stock" {{ old('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
								</option>
								<option value="pre_order" {{ old('stock_status') == 'pre_order' ? 'selected' : '' }}>Pre Order</option>
							</select>
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label for="weight" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weight
									(kg)</label>
								<input type="number" step="0.01" id="weight" name="weight" value="{{ old('weight') }}"
									min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label for="delivery_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Delivery
									Type</label>
								<select id="delivery_type" name="delivery_type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
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
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">W (cm)</label>
								<input type="number" step="0.01" name="width" value="{{ old('width') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">H (cm)</label>
								<input type="number" step="0.01" name="height" value="{{ old('height') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
					</div>
				</div>

				<!-- 9. Marketing Tools -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Marketing</h2>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<label class="flex items-center gap-3 cursor-pointer">
							<input type="checkbox" name="is_featured" value="1" id="is_featured"
								{{ old('is_featured') ? 'checked' : '' }}
								class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Featured Product</span>
						</label>
						<label class="flex items-center gap-3 cursor-pointer">
							<input type="checkbox" name="coupon_eligible" value="1" id="coupon_eligible"
								{{ old('coupon_eligible') ? 'checked' : '' }}
								class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Coupon Eligible</span>
						</label>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tags</label>
							<div class="grid gap-2">
								<input type="text" name="tags[]" value="{{ old('tags.0') }}" maxlength="50" placeholder="Tag 1"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
								<input type="text" name="tags[]" value="{{ old('tags.1') }}" maxlength="50" placeholder="Tag 2"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
								<input type="text" name="tags[]" value="{{ old('tags.2') }}" maxlength="50" placeholder="Tag 3"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
					</div>
				</div>

				<!-- 10. Compliance & Others -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Compliance & Others</h2>
					</div>
					<div class="p-4 sm:p-6 space-y-4">
						<div>
							<label for="return_policy" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Return
								Policy</label>
							<textarea id="return_policy" name="return_policy" rows="2"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('return_policy') }}</textarea>
						</div>
						<div>
							<label for="warranty" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warranty</label>
							<input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}"
								placeholder="e.g., 1 year warranty"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
						<div>
							<label for="manufacturer"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Manufacturer</label>
							<input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
						<div class="flex flex-wrap gap-4">
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="halal_certified" value="1" id="halal_certified"
									{{ old('halal_certified') ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Halal Certified</span>
							</label>
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="organic_certified" value="1" id="organic_certified"
									{{ old('organic_certified') ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Organic</span>
							</label>
						</div>
						<div>
							<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
							<select id="status" name="status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published</option>
								<option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
								<option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const dropArea = document.getElementById('drop-area');
				const input = document.getElementById('images');
				const preview = document.getElementById('image-preview');
				const container = dropArea.parentElement;
				const newImageMap = new Map();

				dropArea.addEventListener('click', () => input.click());

				dropArea.addEventListener('dragover', function(e) {
					e.preventDefault();
					e.stopPropagation();
					dropArea.classList.add('border-blue-500', 'bg-blue-50/50', 'dark:bg-blue-900/20');
				}, false);

				dropArea.addEventListener('dragleave', function(e) {
					e.preventDefault();
					e.stopPropagation();
					dropArea.classList.remove('border-blue-500', 'bg-blue-50/50', 'dark:bg-blue-900/20');
				}, false);

				dropArea.addEventListener('drop', function(e) {
					e.preventDefault();
					e.stopPropagation();
					dropArea.classList.remove('border-blue-500', 'bg-blue-50/50', 'dark:bg-blue-900/20');
					const files = e.dataTransfer.files;
					if (files.length > 0) {
						handleFiles(files);
					}
				}, false);

				input.addEventListener('change', function(e) {
					if (this.files.length > 0) {
						handleFiles(this.files);
					}
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
								<img src="${e.target.result}" class="w-full h-full object-cover" id="img-${fileId}">
								<div class="absolute bottom-1 left-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded">New</div>
								<label class="absolute bottom-1 right-1 cursor-pointer bg-white/90 dark:bg-gray-800 rounded px-1">
									<input type="radio" name="primary_image_new" value="${fileId}" class="w-3 h-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600">
								</label>
								<button type="button" class="remove-preview absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700" data-id="${fileId}" title="Remove">×</button>
							`;
							div.querySelector('.remove-preview').addEventListener('click', function() {
								const id = this.dataset.id;
								newImageMap.delete(id);
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
					newImageMap.forEach(file => {
						dataTransfer.items.add(file);
					});
					input.files = dataTransfer.files;
				}
			});

			function fetchAttributes(catalogId) {
				const section = document.getElementById('dynamic-attributes-section');
				const container = document.getElementById('dynamic-attributes-container');

				if (!catalogId) {
					section.style.display = 'none';
					container.innerHTML = '';
					return;
				}

				section.style.display = 'block';
				container.innerHTML =
					'<div class="flex items-center justify-center py-4"><svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

				fetch(`/admin/products/attributes/${catalogId}`)
					.then(response => response.json())
					.then(data => {
						container.innerHTML = '';
						if (data.attributes && data.attributes.length > 0) {
							data.attributes.forEach((attr) => {
								const div = document.createElement('div');
								const fieldId = `attr_${attr.id}`;
								const fieldName = `custom_attributes[${attr.id}]`;

								let inputHtml = '';
								if (attr.type === 'select' && attr.values && attr.values.length > 0) {
									inputHtml =
										`<select id="${fieldId}" name="${fieldName}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"><option value="">Select ${attr.name}</option>`;
									attr.values.forEach(val => {
										inputHtml += `<option value="${val.id}">${val.value}</option>`;
									});
									inputHtml += '</select>';
								} else if (attr.type === 'multiselect' && attr.values && attr.values.length > 0) {
									inputHtml = '<div class="space-y-2">';
									attr.values.forEach(val => {
										inputHtml +=
											`<label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="${fieldName}[]" value="${val.id}" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"> <span class="text-sm text-gray-700 dark:text-gray-400">${val.value}</span></label>`;
									});
									inputHtml += '</div>';
								} else if (attr.type === 'boolean') {
									inputHtml =
										`<label class="flex items-center gap-3 cursor-pointer"><input type="checkbox" id="${fieldId}" name="${fieldName}" value="1" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"> <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Yes</span></label>`;
								} else if (attr.type === 'text') {
									inputHtml =
										`<input type="text" id="${fieldId}" name="${fieldName}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" placeholder="${attr.name}">`;
								} else if (attr.type === 'number') {
									inputHtml =
										`<input type="number" id="${fieldId}" name="${fieldName}" step="0.01" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" placeholder="${attr.name}">`;
								} else if (attr.type === 'date') {
									inputHtml =
										`<input type="date" id="${fieldId}" name="${fieldName}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">`;
								}

								div.innerHTML = `
									<label for="${fieldId}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">${attr.name}${attr.pivot && attr.pivot.is_required ? ' *' : ''}</label>
									${inputHtml}
								`;
								container.appendChild(div);
							});
						} else {
							container.innerHTML =
								'<p class="text-gray-500 dark:text-gray-400 text-sm">No attributes configured for this category.</p>';
						}
					})
					.catch(error => {
						console.error('Error fetching attributes:', error);
						container.innerHTML = '<p class="text-red-500 text-sm">Error loading attributes.</p>';
					});
			}

			function updateProductTypeInfo() {
				const productType = document.getElementById('product_type').value;
				const variantsSection = document.getElementById('variants-section');

				if (productType === 'variable') {
					variantsSection.innerHTML =
						'<p class="text-blue-600 dark:text-blue-400 text-sm">Variable product - configure attributes above to enable variants engine.</p>';
				} else if (productType === 'digital') {
					variantsSection.innerHTML =
						'<p class="text-gray-500 dark:text-gray-400 text-sm">Digital product - no variants needed.</p>';
				} else {
					variantsSection.innerHTML =
						'<p class="text-gray-500 dark:text-gray-400 text-sm">Simple product - no variants.</p>';
				}
			}
		</script>
	@endpush
@endsection
