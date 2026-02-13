@extends('admin.layouts.app')

@section('title', 'Inventory — Kiddo\'s Heaven')

@section('content')
	<div x-data="inventoryManager({
    initialSearch: '{{ request('search', '') }}',
    baseUrl: '/admin/inventory',
    csrf: '{{ csrf_token() }}'
})">
		<!-- Toast Notification -->
		<div x-show="toastShow" x-transition.opacity.duration.300ms
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
			:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'" style="display: none;">
			<svg x-show="toastType === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<svg x-show="toastType === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
			</svg>
			<span x-text="toastMessage" class="text-sm font-medium"></span>
		</div>

		<!-- Entity Header -->
		<x-admin.ui.entity-header title="Inventory Management" :breadcrumbs="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Inventory', 'url' => null]]">
			<x-slot:actions>
				<button @click="openBulkImportModal()"
					class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
					</svg>
					Import CSV
				</button>
			</x-slot:actions>
		</x-admin.ui.entity-header>

		@php
			$totalProducts = \App\Models\Product::count();
			$lowStockCount = \App\Models\Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
			$outOfStockCount = \App\Models\Product::where('stock_quantity', 0)->count();
			$totalValue = \App\Models\Product::sum(\DB::raw('stock_quantity * price'));
		@endphp

		<!-- Stats Grid -->
		<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
			<x-admin.ui.stat-card title="Total Products" :value="$totalProducts" icon="box" color="blue" :url="route('admin.products.index')" />

			<x-admin.ui.stat-card title="Low Stock" :value="$lowStockCount" icon="alert" color="yellow" :url="route('admin.inventory.index', ['filter' => 'low'])" />

			<x-admin.ui.stat-card title="Out of Stock" :value="$outOfStockCount" icon="x-circle" color="red" :url="route('admin.inventory.index', ['filter' => 'out'])" />

			<x-admin.ui.stat-card title="Total Value" :value="'৳' . number_format($totalValue, 2)" icon="currency" color="green" />
		</div>

		<!-- Filter & Search Bar -->
		<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/3">
			<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
				<div class="flex items-center gap-2">
					<a href="{{ route('admin.inventory.index', ['filter' => 'all', 'search' => request('search', '')]) }}"
						class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $stockFilter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
						</svg>
						All Products
					</a>
					<a href="{{ route('admin.inventory.index', ['filter' => 'low', 'search' => request('search', '')]) }}"
						class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $stockFilter === 'low' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
						</svg>
						Low Stock
					</a>
					<a href="{{ route('admin.inventory.index', ['filter' => 'out', 'search' => request('search', '')]) }}"
						class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $stockFilter === 'out' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
						Out of Stock
					</a>
				</div>

				<form @submit.prevent="searchInventory()">
					<div class="relative">
						<button type="button" @click="searchInventory()" class="absolute -translate-y-1/2 left-3 top-1/2">
							<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" />
							</svg>
						</button>
						<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search products..."
							class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-10 pr-4 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-400 dark:focus:border-blue-600 sm:w-64" />
					</div>
				</form>
			</div>
		</div>

		<!-- Inventory Table -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
			<div class="overflow-hidden">
				<div class="max-w-full overflow-x-auto">
					<table class="min-w-full">
						<thead>
							<tr class="border-gray-200 border-y dark:border-gray-700">
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Product</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">SKU
								</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Price</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Stock</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Status</th>
								<th scope="col" class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@forelse ($products as $product)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors"
									onclick="window.location='{{ route('admin.products.show', $product) }}'">
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center gap-3">
											@if ($product->primary_image)
												<img src="{{ Storage::url($product->primary_image) }}" alt="{{ $product->name }}"
													class="w-10 h-10 rounded-lg object-cover">
											@elseif($product->images && count($product->images) > 0)
												<img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
													class="w-10 h-10 rounded-lg object-cover">
											@else
												<div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
													<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
													</svg>
												</div>
											@endif
											<div>
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">
													{{ $product->variants_count ?? 0 }} variant(s)
												</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm font-mono text-gray-900 dark:text-white">{{ $product->sku ?? 'N/A' }}</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm font-semibold text-gray-900 dark:text-white">
											৳{{ number_format($product->price, 2) }}</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->stock_quantity }}</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										@if ($product->stock_quantity <= 0)
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400">
												<span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
												Out of Stock
											</span>
										@elseif($product->stock_quantity <= 10)
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
												<span class="w-1.5 h-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span>
												Low Stock
											</span>
										@else
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400">
												<span class="w-1.5 h-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
												In Stock
											</span>
										@endif
									</td>
									<td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
										<button
											@click.stop="openStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock_quantity }})"
											class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Update Stock">
											<svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
											</svg>
										</button>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="px-6 py-16">
										<x-admin.ui.empty-state icon="package" title="No products found"
											description="Try adjusting your filters or search query." :showAction="false" />
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>

			<!-- Pagination -->
			@if ($products->hasPages())
				<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
					<div class="flex items-center justify-between">
						<a href="{{ $products->appends(request()->query())->previousPageUrl() }}" @class([
							'flex items-center gap-2 rounded-lg border bg-white px-4 py-2.5 text-sm font-medium shadow-sm transition-colors dark:bg-gray-800',
							'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700' => $products->previousPageUrl(),
							'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-800 dark:text-gray-600' => !$products->previousPageUrl(),
						])>
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
									fill="currentColor" />
							</svg>
							<span class="hidden sm:inline">Previous</span>
						</a>

						<span class="text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
							{{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

						<ul class="hidden items-center gap-1 sm:flex">
							@foreach ($products->appends(request()->query())->links()->elements[0] as $page => $url)
								<li>
									<a href="{{ $url }}" @class([
										'flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium transition-colors',
										'bg-blue-500 text-white' => $page == $products->currentPage(),
										'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' =>
											$page != $products->currentPage(),
									])>
										{{ $page }}
									</a>
								</li>
							@endforeach
						</ul>

						<a href="{{ $products->appends(request()->query())->nextPageUrl() }}" @class([
							'flex items-center gap-2 rounded-lg border bg-white px-4 py-2.5 text-sm font-medium shadow-sm transition-colors dark:bg-gray-800',
							'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700' => $products->nextPageUrl(),
							'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-800 dark:text-gray-600' => !$products->nextPageUrl(),
						])>
							<span class="hidden sm:inline">Next</span>
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
									fill="currentColor" />
							</svg>
						</a>
					</div>
				</div>
			@endif
		</div>

		<!-- Stock Update Modal -->
		<div x-show="showModal" x-transition.opacity.duration.300ms style="display: none;"
			class="fixed inset-0 z-99999 flex items-center justify-center px-4" x-cloak>
			<div class="fixed inset-0 bg-black/50" @click="closeStockModal()"></div>
			<div
				class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
				@click.stop>
				<div class="flex items-center justify-between mb-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update Stock</h3>
					<button @click="closeStockModal()"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form id="stockForm" method="POST" @submit.prevent="updateStock()">
					@csrf
					<div class="mb-4">
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
							Product: <span x-text="productName" class="font-medium text-gray-900 dark:text-white"></span>
						</p>
						<label for="stockQuantity" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New
							Quantity *</label>
						<input type="number" name="quantity" id="stockQuantity" x-model="stockQuantity" required min="0"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-gray-400 dark:focus:border-blue-600">
					</div>
					<div class="flex justify-end gap-3">
						<button type="button" @click="closeStockModal()"
							class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Cancel</button>
						<button type="submit"
							class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 dark:hover:bg-blue-600 transition-colors">
							<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							Update Stock
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('inventoryManager', (config) => ({
				searchTerm: config.initialSearch,
				showModal: false,
				productId: null,
				productName: '',
				stockQuantity: 0,
				toastMessage: '',
				toastType: 'success',
				toastShow: false,

				showToast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.toastShow = true;
					setTimeout(() => {
						this.toastShow = false;
					}, 3000);
				},

				searchInventory() {
					const url = new URL(window.location);
					url.searchParams.set('search', this.searchTerm);
					url.searchParams.set('page', 1);
					window.location.href = url.toString();
				},

				handleKeydown(e) {
					if (e.key === 'Enter') this.searchInventory();
				},

				openStockModal(id, name, quantity) {
					this.productId = id;
					this.productName = name;
					this.stockQuantity = quantity;
					this.showModal = true;
				},

				closeStockModal() {
					this.showModal = false;
					this.productId = null;
					this.productName = '';
					this.stockQuantity = 0;
				},

				async updateStock() {
					try {
						const response = await fetch(
							`/admin/inventory/update`, {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'X-CSRF-TOKEN': config.csrf,
								},
								body: JSON.stringify({
									product_id: this.productId,
									quantity: this.stockQuantity,
									action: 'set'
								}),
							});
						const data = await response.json();
						if (data.success) {
							this.showToast(data.message || 'Stock updated successfully!');
							this.closeStockModal();
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						} else {
							this.showToast(data.message || 'Error updating stock', 'error');
						}
					} catch (error) {
						console.error('Error:', error);
						this.showToast('Error updating stock', 'error');
					}
				},

				init() {
					this.$watch('showModal', (value) => {
						if (value) {
							document.body.style.overflow = 'hidden';
						} else {
							document.body.style.overflow = '';
						}
					});
				},
			}))
		})
	</script>
@endpush
