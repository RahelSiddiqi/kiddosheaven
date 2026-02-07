@extends('admin.layouts.app')

@section('title', 'Products — Admin')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Products</h3>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<a href="{{ route('admin.products.create') }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Product
						</a>
					</div>
				</div>

				@if ($products->isEmpty())
					<div class="px-5 pb-5">
						<p class="text-gray-500 dark:text-gray-400">No products yet. <a href="{{ route('admin.products.create') }}"
								class="text-[--admin-primary] underline hover:text-[--admin-accent]">Add your first product</a>
						</p>
					</div>
				@else
					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Image</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Name</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Category</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Price</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Cost</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Profit</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Featured</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($products as $product)
										<tr>
											<td class="py-4 whitespace-nowrap">
												@php
													$img = $product->primary_image ?? ($product->images[0] ?? null);
												@endphp
												@if ($img)
													<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
												@else
													<div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-gray-400">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
															stroke="currentColor">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4z" />
														</svg>
													</div>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">
													{{ $product->name }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													{{ $product->catalog->name ?? '-' }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">
													${{ number_format($product->price / 100, 2) }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													${{ number_format(($product->cost_price ?? 0) / 100, 2) }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php
													$profit = $product->profit_margin;
													$colorClass = $profit >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500';
												@endphp
												<div class="text-sm font-medium {{ $colorClass }}">
													{{ $profit !== null ? number_format($profit, 1) . '%' : 'N/A' }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($product->is_featured)
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
														Active
													</span>
												@else
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
														Inactive
													</span>
												@endif
											</td>
											<td class="py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<a href="{{ route('admin.products.edit', $product) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.edit />
													</a>
													<form action="{{ route('admin.products.destroy', $product) }}" method="post" class="inline">
														@csrf
														@method('DELETE')
														<button type="submit" @click="deleteProduct({{ $product->id }})"
															class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
															<x-icons.delete />
														</button>
													</form>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="mt-6 px-5 pb-5">
						{{ $products->links('vendor.pagination.default') }}
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
		x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div @click="showDeleteModal = false" class="fixed inset-0 bg-black/50 transition-opacity"></div>
			<div
				class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
				<div class="text-center">
					<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
						<svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
						</svg>
					</div>
					<h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Delete Product</h3>
					<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this product? This action
						cannot be undone.</p>
					<div class="flex gap-3">
						<button @click="showDeleteModal = false"
							class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button @click="confirmDelete()"
							class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		function deleteProduct(id) {
			event.preventDefault();
			window.deleteProductId = id;
			window.showDeleteModal = true;
		}

		function confirmDelete() {
			if (window.deleteProductId) {
				const form = document.querySelector('form[action*="/products/' + window.deleteProductId + '"]');
				if (form) {
					form.submit();
				}
			}
		}
	</script>
@endsection
