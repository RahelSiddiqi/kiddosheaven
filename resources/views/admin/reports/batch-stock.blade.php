@extends('admin.layouts.app')

@section('title', 'Batch Stock Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Batch Stock Report</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View stock levels by purchase batch with FIFO/LIFO costing
						</p>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<form method="GET" class="flex gap-2">
							<select name="product_id" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Products</option>
								@foreach ($products as $product)
									<option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
										{{ $product->name }}
									</option>
								@endforeach
							</select>
						</form>
						@if ($report)
							<a href="{{ route('admin.reports.batch-stock.export', ['product_id' => request('product_id')]) }}"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333M15.8333 15.8333L10.8333 10.8333M15.8333 4.16667L10.8333 10.1667"
										stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
								Export CSV
							</a>
						@endif
					</div>
				</div>

				@if ($report)
					<!-- Summary Stats -->
					<div class="px-6 pb-4">
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Batches</div>
								<div class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ count($report['batches']) }}</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Received</div>
								<div class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
									{{ number_format($report['total_quantity']) }}</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Remaining Stock</div>
								<div class="mt-1 font-bold text-green-600 text-title-sm">{{ number_format($report['remaining_quantity']) }}</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Value</div>
								<div class="mt-1 font-bold text-blue-600 text-title-sm">{{ number_format($report['total_value'], 2) }} BDT</div>
							</div>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Batch #</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Purchase Date</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Unit
											Cost</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Received</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Remaining</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Sold
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Batch Value</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Expiry</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($report['batches'] as $batch)
										<tr>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $batch->batch_number }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													{{ $batch->purchase_date ? \Carbon\Carbon::parse($batch->purchase_date)->format('M d, Y') : '-' }}
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($batch->unit_cost, 2) }} BDT</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ $batch->quantity_received }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-bold text-gray-900 dark:text-white">{{ $batch->remaining_quantity }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-red-600">{{ $batch->quantity_received - $batch->remaining_quantity }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-bold text-blue-600">
													{{ number_format($batch->remaining_quantity * $batch->unit_cost, 2) }} BDT</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													@if ($batch->expiry_date)
														{{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}
														@if (\Carbon\Carbon::parse($batch->expiry_date)->isPast())
															<span class="text-red-500 ml-1">⚠ Expired</span>
														@elseif(\Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 30)
															<span class="text-yellow-500 ml-1">⚠ Soon</span>
														@endif
													@else
														-
													@endif
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@else
					<div class="px-6 py-12 text-center">
						<svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
						<h4 class="text-lg font-medium text-gray-800 dark:text-white/90">Select a Product</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a product from the dropdown to view batch stock
							details</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
