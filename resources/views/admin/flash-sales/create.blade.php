@extends('admin.layouts.app')

@section('title', 'Create Flash Sale — Kiddo\'s Heaven')

@section('content')
	<!-- Alpine.js wrapper for product modal -->
	<div x-data="{ productModalOpen: false }">

		<div class="grid grid-cols-12 gap-4 md:gap-6">
			<div class="col-span-12">
				<form action="{{ route('admin.flash-sales.store') }}" method="POST" id="flashSaleForm">
					@csrf

					<!-- Header -->
					<div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
						<div>
							<a href="{{ route('admin.flash-sales.index') }}"
								class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-1">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
								</svg>
								Back
							</a>
							<h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Flash Sale</h1>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Create a limited-time flash sale to boost your sales</p>
						</div>
						<div class="flex gap-3">
							<a href="{{ route('admin.flash-sales.index') }}"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								Cancel
							</a>
							<button type="submit"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
								Create Flash Sale
							</button>
						</div>
					</div>

					<div class="grid gap-6 lg:grid-cols-12">
						<!-- Left Column (8 cols) - Form -->
						<div class="lg:col-span-8 space-y-6">
							<!-- Basic Info -->
							<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
								<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Basic Information</h3>
								<div class="space-y-4">
									<!-- Name and Discount inline -->
									<div class="grid gap-4 sm:grid-cols-2">
										<div>
											<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Flash Sale Name *</label>
											<input type="text" id="name" name="name"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
												placeholder="e.g., Summer Flash Sale" required value="{{ old('name') }}">
											@error('name')
												<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
											@enderror
										</div>
										<div>
											<label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Percentage *</label>
											<div class="relative">
												<input type="number" id="discount_percentage" name="discount_percentage"
													class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
													placeholder="10" min="1" max="99" step="0.01" required value="{{ old('discount_percentage') }}">
												<span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">%</span>
											</div>
											@error('discount_percentage')
												<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
											@enderror
										</div>
									</div>
									<div>
										<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
										<textarea id="description" name="description" rows="2"
											class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="Optional description">{{ old('description') }}</textarea>
									</div>
								</div>
							</div>

							<!-- Schedule -->
							<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
								<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Schedule</h3>
								<div class="grid gap-4 sm:grid-cols-2">
									<div>
										<label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date & Time *</label>
										<input type="datetime-local" id="starts_at" name="starts_at"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
											required value="{{ old('starts_at') }}">
										@error('starts_at')
											<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
										@enderror
									</div>
									<div>
										<label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date & Time *</label>
										<input type="datetime-local" id="ends_at" name="ends_at"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
											required value="{{ old('ends_at') }}">
										@error('ends_at')
											<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
										@endif
									</div>
								</div>
							</div>
						</div>

						<!-- Right Column (4 cols) - Products -->
						<div class="lg:col-span-4 space-y-6">
							<!-- Products Selection -->
							<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
								<div class="flex items-center justify-between mb-4">
									<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Products</h3>
									<button type="button" @click="productModalOpen = true"
										class="h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
										<svg class="mr-1" width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										Add
									</button>
								</div>

								<!-- Selected Products -->
								<div id="selectedProductsContainer">
									@if ($products->isEmpty())
										<div class="text-center py-6">
											<svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
											</svg>
											<p class="text-sm text-gray-500 dark:text-gray-400">No products available</p>
										</div>
									@else
										<div id="noProductsSelected" class="text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
											<svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
											</svg>
											<p class="text-sm text-gray-500 dark:text-gray-400">No products selected</p>
										</div>
										<div id="selectedProductsList" class="hidden space-y-2"></div>
									@endif
								</div>
							</div>

							<!-- Tips -->
							<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
								<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Tips</h3>
								<ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
									<li class="flex items-start gap-2">
										<svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span>Flash sales typically last 24-48 hours</span>
									</li>
									<li class="flex items-start gap-2">
										<svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span>Discounts between 20-50% work best</span>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Product Selection Modal - INSIDE FORM -->
					<div x-show="productModalOpen"
						x-cloak
						class="fixed inset-0 z-50 overflow-y-auto"
						style="display: none;">
						<div class="flex min-h-screen items-center justify-center p-4">
							<div @click="productModalOpen = false" class="fixed inset-0 bg-black/50 transition-opacity"></div>
							<div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 shadow-xl z-50">

								<!-- Header -->
								<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
									<h3 class="text-lg font-semibold text-gray-800 dark:text-white">Select Products</h3>
									<button type="button" @click="productModalOpen = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
										<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
										</svg>
									</button>
								</div>

								<!-- Body -->
								<div class="p-5">
									<!-- Search -->
									<div class="relative mb-4">
										<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
										</svg>
										<input type="text" id="modalProductSearch" placeholder="Search products..."
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
									</div>

									<!-- Products List - CHECKBOXES INSIDE FORM -->
									<div class="max-h-80 overflow-y-auto space-y-2" id="modalProductsList">
										@forelse ($products as $product)
											<label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 cursor-pointer transition dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 searchable-product"
												data-name="{{ strtolower($product->name) }}">
												<input type="checkbox" name="products[]" value="{{ $product->id }}"
													class="product-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
													onchange="handleProductCheckboxChange(this)">
												<div class="h-10 w-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
													@if ($product->primary_image)
														<img src="{{ asset('storage/' . $product->primary_image) }}" alt="{{ $product->name }}"
															class="h-8 w-8 rounded object-cover">
													@else
														<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
														</svg>
													@endif
												</div>
												<div class="flex-1 min-w-0">
													<p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
													<p class="text-xs text-gray-500 dark:text-gray-400">Stock: {{ $product->stock_quantity }} | ৳{{ number_format($product->price / 100, 2) }}</p>
												</div>
											</label>
										@empty
											<div class="text-center py-8">
												<p class="text-gray-500 dark:text-gray-400">No products available</p>
											</div>
										@endforelse
									</div>
								</div>

								<!-- Footer -->
								<div class="flex justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
									<button type="button" @click="productModalOpen = false"
										class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
										Cancel
									</button>
									<button type="button" @click="productModalOpen = false; updateSelectedProductsDisplay();"
										class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
										Done
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const modalProductSearch = document.getElementById('modalProductSearch');
			const searchableProducts = document.querySelectorAll('.searchable-product');

			if (modalProductSearch) {
				modalProductSearch.addEventListener('input', function() {
					const searchTerm = this.value.toLowerCase();
					searchableProducts.forEach(item => {
						const name = item.dataset.name;
						if (name.includes(searchTerm)) {
							item.style.display = '';
						} else {
							item.style.display = 'none';
						}
					});
				});
			}
		});

		function handleProductCheckboxChange(checkbox) {
			updateSelectedProductsDisplay();
		}

		function updateSelectedProductsDisplay() {
			const checkboxes = document.querySelectorAll('.product-checkbox:checked');
			const noProductsMsg = document.getElementById('noProductsSelected');
			const productsList = document.getElementById('selectedProductsList');

			if (checkboxes.length === 0) {
				if (noProductsMsg) noProductsMsg.classList.remove('hidden');
				if (productsList) {
					productsList.classList.add('hidden');
					productsList.innerHTML = '';
				}
			} else {
				if (noProductsMsg) noProductsMsg.classList.add('hidden');
				if (productsList) {
					productsList.classList.remove('hidden');
					let html = '';
					checkboxes.forEach(cb => {
						const label = cb.closest('label');
						const name = label.querySelector('p.font-medium').textContent;
						const price = label.querySelector('p.text-xs').textContent;
						const productId = cb.value;
						html += `
							<div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
								<div class="h-10 w-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
									${label.querySelector('img') ? label.querySelector('img').outerHTML : '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'}
								</div>
								<div class="flex-1 min-w-0">
									<p class="font-medium text-sm text-gray-900 dark:text-white truncate">${name}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400 truncate">${price}</p>
								</div>
								<button type="button" onclick="removeSelectedProduct(${productId})" class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg">
									<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
									</svg>
								</button>
							</div>
						`;
					});
					productsList.innerHTML = html;
				}
			}
		}

		function removeSelectedProduct(productId) {
			const checkbox = document.querySelector(`.product-checkbox[value="${productId}"]`);
			if (checkbox) {
				checkbox.checked = false;
			}
			updateSelectedProductsDisplay();
		}
	</script>
@endsection
