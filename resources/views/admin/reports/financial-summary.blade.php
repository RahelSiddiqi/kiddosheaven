@extends('admin.layouts.app')

@section('title', 'Financial Summary — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Stats Overview -->
		<div class="col-span-12 lg:col-span-3">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Net Profit</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ number_format($netProfit, 2) }} BDT
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-3">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Stock Valuation</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ number_format($stockValuation, 2) }} BDT
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-3">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Partner Payables</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ number_format($partnerPayables->sum('total_payable'), 2) }} BDT
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-3">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Active Partners</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ $partnerPayables->count() }}
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Main Report Card -->
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Financial Summary Report</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comprehensive financial overview for
							{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
					</div>
					<form method="GET" class="flex gap-2 items-end">
						<div>
							<label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Start
								Date</label>
							<input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">End Date</label>
							<input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Apply
						</button>
					</form>
				</div>

				<!-- Financial Breakdown -->
				<div class="p-5">
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
						<!-- Revenue Section -->
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
							<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
								<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								Revenue
							</h4>
							<div class="space-y-3">
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">Gross Sales</span>
									<span
										class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($netProfit['gross_sales'] ?? 0, 2) }}
										BDT</span>
								</div>
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">Returns & Refunds</span>
									<span class="text-sm font-medium text-red-600">-{{ number_format($netProfit['returns'] ?? 0, 2) }} BDT</span>
								</div>
								<div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
									<span class="text-sm font-semibold text-gray-800 dark:text-white/90">Net Revenue</span>
									<span class="text-sm font-bold text-green-600">{{ number_format($netProfit['net_revenue'] ?? 0, 2) }}
										BDT</span>
								</div>
							</div>
						</div>

						<!-- Cost Section -->
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
							<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
								<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
								</svg>
								Costs
							</h4>
							<div class="space-y-3">
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">COGS (Cost of Goods)</span>
									<span
										class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($netProfit['cogs'] ?? 0, 2) }}
										BDT</span>
								</div>
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">Operating Expenses</span>
									<span
										class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($netProfit['operating_expenses'] ?? 0, 2) }}
										BDT</span>
								</div>
								<div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
									<span class="text-sm font-semibold text-gray-800 dark:text-white/90">Total Costs</span>
									<span class="text-sm font-bold text-red-600">{{ number_format($netProfit['total_costs'] ?? 0, 2) }} BDT</span>
								</div>
							</div>
						</div>

						<!-- Profit Section -->
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
							<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
								<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
								</svg>
								Profitability
							</h4>
							<div class="space-y-3">
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">Gross Profit</span>
									<span class="text-sm font-medium text-green-600">{{ number_format($netProfit['gross_profit'] ?? 0, 2) }}
										BDT</span>
								</div>
								<div class="flex justify-between">
									<span class="text-sm text-gray-500 dark:text-gray-400">Net Profit</span>
									<span
										class="text-sm font-medium {{ ($netProfit['net_profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($netProfit['net_profit'] ?? 0, 2) }}
										BDT</span>
								</div>
								<div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
									<span class="text-sm font-semibold text-gray-800 dark:text-white/90">Profit Margin</span>
									<span
										class="text-sm font-bold {{ ($netProfit['profit_margin'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($netProfit['profit_margin'] ?? 0, 1) }}%</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Partner Payables -->
				@if ($partnerPayables->count() > 0)
					<div class="px-5 pb-5">
						<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-4">Partner Payables</h4>
						<div class="max-w-full overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Partner</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Type</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Balance</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Total Payable</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($partnerPayables as $payable)
										<tr>
											<td class="px-4 py-3">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payable['name'] }}</div>
											</td>
											<td class="px-4 py-3">
												<span
													class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payable['type'] == 'partner' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
													{{ ucfirst($payable['type']) }}
												</span>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-gray-900 dark:text-white">{{ number_format($payable['balance'], 2) }} BDT</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm font-bold text-blue-600">{{ number_format($payable['total_payable'], 2) }} BDT</div>
											</td>
											<td class="px-4 py-3">
												@if ($payable['total_payable'] > 0)
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-600">
														Pending
													</span>
												@else
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-600">
														Cleared
													</span>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
