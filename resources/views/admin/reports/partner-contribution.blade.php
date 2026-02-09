@extends('admin.layouts.app')

@section('title', 'Partner Contribution Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Partner Contribution Report</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track partner contributions and profit sharing</p>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<form method="GET" class="flex gap-2 items-end">
							<select name="partner_id" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">Select Partner</option>
								@foreach ($partners as $partner)
									<option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
										{{ $partner->name }}
									</option>
								@endforeach
							</select>
							<div class="flex gap-2">
								<input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
									class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
									class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<button type="submit"
									class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
									Filter
								</button>
							</div>
						</form>
					</div>
				</div>

				@if ($report)
					<!-- Summary Stats -->
					<div class="px-6 pb-4">
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<div class="rounded-xl border border-gray-200 bg-blue-50 p-4 dark:border-gray-700 dark:bg-blue-500/10">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</div>
								<div class="mt-1 font-bold text-blue-600 text-title-sm">{{ number_format($report['total_revenue'], 2) }} BDT
								</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-red-50 p-4 dark:border-gray-700 dark:bg-red-500/10">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Cost</div>
								<div class="mt-1 font-bold text-red-600 text-title-sm">{{ number_format($report['total_cost'], 2) }} BDT</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-green-50 p-4 dark:border-gray-700 dark:bg-green-500/10">
								<div class="text-sm text-gray-500 dark:text-gray-400">Gross Profit</div>
								<div class="mt-1 font-bold text-green-600 text-title-sm">{{ number_format($report['gross_profit'], 2) }} BDT
								</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-purple-50 p-4 dark:border-gray-700 dark:bg-purple-500/10">
								<div class="text-sm text-gray-500 dark:text-gray-400">Partner Share ({{ $report['commission_rate'] }}%)</div>
								<div class="mt-1 font-bold text-purple-600 text-title-sm">{{ number_format($report['partner_commission'], 2) }}
									BDT</div>
							</div>
						</div>
					</div>

					<!-- Partner Info -->
					@if ($selectedPartner)
						<div class="px-6 pb-4">
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<h4 class="font-semibold text-gray-800 dark:text-white/90">{{ $selectedPartner->name }}</h4>
								<p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedPartner->email }}</p>
								<div class="mt-2 flex gap-4 text-sm">
									<span><strong>Commission Rate:</strong> {{ $report['commission_rate'] }}%</span>
									<span><strong>Total Orders:</strong> {{ $report['total_orders'] }}</span>
								</div>
							</div>
						</div>
					@endif

					<!-- Top Products Table -->
					<div class="px-6 pb-4">
						<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-3">Top Products by Revenue</h4>
						<div class="max-w-full overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Product</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Quantity Sold</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Revenue</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Cost
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Profit</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($report['top_products'] as $product)
										<tr>
											<td class="px-4 py-3">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product['name'] }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ $product['quantity_sold'] }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-blue-600 font-medium">{{ number_format($product['revenue'], 2) }} BDT</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-red-600">{{ number_format($product['cost'], 2) }} BDT</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-green-600 font-medium">{{ number_format($product['profit'], 2) }} BDT</div>
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
								d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
						</svg>
						<h4 class="text-lg font-medium text-gray-800 dark:text-white/90">Select a Partner</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a partner to view their contribution report</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
