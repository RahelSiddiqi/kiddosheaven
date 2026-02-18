@extends('admin.layouts.app')

@section('title', $flashSale->name . ' — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Header with Back -->
			<div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<a href="{{ route('admin.marketing.flash-sales.index') }}"
						class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-1">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
						</svg>
						Back to Flash Sales
					</a>
					<h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $flashSale->name }}</h1>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $flashSale->description ?? 'No description' }}</p>
				</div>
				<div class="flex gap-3">
					<a href="{{ route('admin.marketing.flash-sales.edit', $flashSale) }}"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path
								d="M11.8333 2.29169L15.4167 5.87502M13.4167 2.87502C13.8564 2.43534 14.438 2.16669 16.0417 2.16669M5.37501 14.7084L2.29167 17.7917C2.10542 17.978 2 18.2183 2 18.4688V18.5C2 18.7768 2.22321 19 2.5 19H2.53125C2.78175 19 3.02208 18.8946 3.20833 18.7084L6.29167 15.625C6.41812 15.4985 6.51634 15.3477 6.58033 15.1833L13.8333 3.18335C14.0296 2.86252 14.0373 2.44568 13.8521 2.12252L11.8333 2.29169Z"
								stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						Edit
					</a>
					<form action="{{ route('admin.marketing.flash-sales.destroy', $flashSale) }}" method="POST" class="inline">
						@csrf
						@method('DELETE')
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-red-500 bg-red-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600"
							onclick="return confirm('Are you sure you want to delete this flash sale?')">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M7.5 5.83333C7.5 5.38611 7.92322 4.91667 8.79167 4.91667H11.2083C12.0768 4.91667 12.5 5.38611 12.5 5.83333M7.5 5.83333V15.8333M7.5 5.83333C7.5 5.38611 7.92322 4.91667 8.79167 4.91667H11.2083C12.0768 4.91667 12.5 5.38611 12.5 5.83333M12.5 5.83333V15.8333M7.5 5.83333H5.41667C5.00111 5.83333 4.79167 6.02778 4.79167 6.39167C4.79167 6.75556 5.00111 6.95833 5.41667 6.95833H14.5833C14.9989 6.95833 15.2083 6.75556 15.2083 6.39167C15.2083 6.02778 14.9989 5.83333 14.5833 5.83333H12.5M10.4167 9.16667V13.75M9.16667 11.4583L10.4167 9.16667L11.6667 11.4583"
									stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							Delete
						</button>
					</form>
				</div>
			</div>

			<!-- Status Banner -->
			@php
				$status = $flashSale->status;
				$statusColors = [
				    'scheduled' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
				    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
				    'ended' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
				];
				$statusColor = $statusColors[$status] ?? $statusColors['scheduled'];
				$statusIcons = [
				    'scheduled' =>
				        '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
				    'active' =>
				        '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
				    'ended' =>
				        '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>',
				];
				$statusIcon = $statusIcons[$status] ?? $statusIcons['scheduled'];
			@endphp

			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3 mb-6">
				<div class="flex flex-wrap items-center justify-between gap-4">
					<div class="flex items-center gap-4">
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
							{!! $statusIcon !!}
							{{ ucfirst($status) }}
						</span>
						<div>
							<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $flashSale->discount_percentage }}% Discount</p>
						</div>
					</div>
					@if ($status === 'active')
						<div class="flex items-center gap-2 text-sm">
							<span class="text-gray-500 dark:text-gray-400">Time Remaining:</span>
							<span id="countdown" class="font-mono font-bold text-lg text-red-500"
								data-end="{{ $flashSale->ends_at->toIso8601String() }}">
								--:--:--
							</span>
						</div>
					@endif
				</div>
			</div>

			<!-- Stats Cards -->
			<div class="grid gap-6 md:grid-cols-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Discount</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $flashSale->discount_percentage }}%</p>
						</div>
						<div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30">
							<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Products</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $flashSale->products->count() }}</p>
						</div>
						<div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30">
							<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
							</svg>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Time</p>
							<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $flashSale->starts_at->format('M d, Y H:i') }}
							</p>
						</div>
						<div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30">
							<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">End Time</p>
							<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $flashSale->ends_at->format('M d, Y H:i') }}
							</p>
						</div>
						<div class="p-3 rounded-xl bg-orange-100 dark:bg-orange-900/30">
							<svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Products in Flash Sale -->
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
				<div
					class="flex flex-col gap-2 p-5 border-b border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Products in Flash Sale</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ $flashSale->products->count() }} products with
							{{ $flashSale->discount_percentage }}% discount</p>
					</div>
				</div>

				@if ($flashSale->products->count() > 0)
					<div class="overflow-x-auto">
						<table class="min-w-full">
							<thead>
								<tr class="border-b border-gray-200 dark:border-gray-700">
									<th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Product</th>
									<th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Original Price</th>
									<th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Flash Price</th>
									<th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Savings</th>
									<th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Stock</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@foreach ($flashSale->products as $product)
									@php
										$originalPrice = $product->price / 100;
										$flashPrice = ($originalPrice * (100 - $flashSale->discount_percentage)) / 100;
										$savings = $originalPrice - $flashPrice;
									@endphp
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
										<td class="py-4 px-4">
											<div class="flex items-center gap-3">
												@if ($product->primary_image)
													<img src="{{ asset('storage/' . $product->primary_image) }}" alt="{{ $product->name }}"
														class="h-12 w-12 rounded-lg object-cover">
												@else
													<div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
														<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
														</svg>
													</div>
												@endif
												<div>
													<p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
													@if ($product->sku)
														<p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
													@endif
												</div>
											</div>
										</td>
										<td class="py-4 px-4 text-right text-sm text-gray-600 dark:text-gray-400">
													৳{{ number_format($originalPrice, 0) }}
										</td>
										<td class="py-4 px-4 text-right text-sm font-semibold text-green-600 dark:text-green-400">
													৳{{ number_format($flashPrice, 0) }}
										</td>
										<td class="py-4 px-4 text-right text-sm font-medium text-red-500">
													-৳{{ number_format($savings, 0) }}
										</td>
										<td class="py-4 px-4 text-right">
											@if ($product->stock_quantity <= 5)
												<span
													class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
													{{ $product->stock_quantity }} left
												</span>
											@elseif($product->stock_quantity <= 20)
												<span
													class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
													{{ $product->stock_quantity }} left
												</span>
											@else
												<span
													class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
													{{ $product->stock_quantity }} left
												</span>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="py-12 text-center">
						<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No products added to this flash sale yet.</p>
						<a href="{{ route('admin.marketing.flash-sales.edit', $flashSale) }}"
							class="inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							<svg class="mr-2" width="16" height="16" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Products
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const countdownElement = document.getElementById('countdown');

				if (countdownElement) {
					const endTime = new Date(countdownElement.dataset.end).getTime();

					function updateCountdown() {
						const now = new Date().getTime();
						const distance = endTime - now;

						if (distance < 0) {
							countdownElement.textContent = 'Expired';
							countdownElement.classList.remove('text-red-500');
							countdownElement.classList.add('text-gray-500');
							return;
						}

						const days = Math.floor(distance / (1000 * 60 * 60 * 24));
						const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						const seconds = Math.floor((distance % (1000 * 60)) / 1000);

						let timeString = '';
						if (days > 0) {
							timeString += days + 'd ';
						}
						timeString += hours.toString().padStart(2, '0') + ':';
						timeString += minutes.toString().padStart(2, '0') + ':';
						timeString += seconds.toString().padStart(2, '0');

						countdownElement.textContent = timeString;
					}

					updateCountdown();
					setInterval(updateCountdown, 1000);
				}
			});
		</script>
	@endpush
@endsection
