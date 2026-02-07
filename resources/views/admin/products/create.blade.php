@extends('admin.layouts.app')

@section('title', 'Create Product — Kiddo\'s Heaven')

@section('content')
	<!-- Toast Notification -->
	@if (session('success'))
		<div x-data="{ show: true }" x-show="show" x-transition
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-70"
			style="display: none;">
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
					<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
					</svg>
					Create Product
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
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('catalog_id') border-red-500 @enderror">
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

						<div>
							<label for="short_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Short
								Description</label>
							<input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}"
								maxlength="500"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('short_description') border-red-500 @enderror">
							@error('short_description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
							<textarea id="description" name="description" rows="3"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('description') }}</textarea>
							@error('description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Media -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Images</h2>
					</div>
					<div class="p-6">
						<!-- All items in one row: new uploads + drop zone -->
						<div class="flex flex-wrap gap-3 items-center">
							<!-- New uploads preview -->
							<div id="image-preview" class="flex flex-wrap gap-3"></div>

							<!-- Drop zone at the end -->
							<div id="drop-area"
								class="w-40 h-30 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all flex-shrink-0 dark:border-gray-700 dark:bg-gray-800/50 dark:hover:bg-gray-800">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
								<input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
							</div>
						</div>
						@error('images')
							<p class="text-red-500 text-sm mt-2">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<!-- SEO -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">SEO</h2>
					</div>
					<div class="p-6 space-y-4">
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
								<label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
									Title</label>
								<input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('meta_title') border-red-500 @enderror">
								@error('meta_title')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
							<div>
								<label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta
									Description</label>
								<textarea id="meta_description" name="meta_description" maxlength="500" rows="2"
								 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('meta_description') }}</textarea>
								@error('meta_description')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Right Column - Sidebar (4 cols) -->
			<div class="lg:col-span-4 space-y-4 md:space-y-6">
				<!-- Pricing & Inventory -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pricing & Inventory</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Price (BDT)
								*</label>
							<input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}"
								min="1" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('price') border-red-500 @enderror">
							@error('price')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="discount_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Discount
								Price (BDT)</label>
							<input type="number" step="0.01" id="discount_price" name="discount_price"
								value="{{ old('discount_price') }}" min="0"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('discount_price') border-red-500 @enderror">
							@error('discount_price')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
								Quantity *</label>
							<input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
								min="0" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('stock_quantity') border-red-500 @enderror">
							@error('stock_quantity')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="cost_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cost Price (BDT) *</label>
							<input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', 0) }}"
								min="0" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('cost_price') border-red-500 @enderror">
							@error('cost_price')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Options -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Options</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">SKU</label>
							<input type="text" id="sku" name="sku" value="{{ old('sku') }}" maxlength="100"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('sku')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
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
						<div class="grid grid-cols-3 gap-2">
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">L</label>
								<input type="number" step="0.01" name="length" value="{{ old('length') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">W</label>
								<input type="number" step="0.01" name="width" value="{{ old('width') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div>
								<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">H</label>
								<input type="number" step="0.01" name="height" value="{{ old('height') }}" min="0"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
						</div>
						<div>
							<label for="weight" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weight
								(kg)</label>
							<input type="number" step="0.01" id="weight" name="weight" value="{{ old('weight') }}"
								min="0"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('weight') border-red-500 @enderror">
							@error('weight')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Status -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Status</h2>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
							<select id="status" name="status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
							</select>
						</div>
						<label class="flex items-center gap-3 cursor-pointer">
							<input type="checkbox" name="is_featured" value="1" id="is_featured"
								{{ old('is_featured') ? 'checked' : '' }}
								class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Featured</span>
						</label>
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
					// Update the file input with all files from newImageMap
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
		</script>
	@endpush
@endsection
