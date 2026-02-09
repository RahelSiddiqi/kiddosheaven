@extends('admin.layouts.app')

@section('title', 'Investor ROI Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Investor ROI Report</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $startDate->format('M d, Y') }} -
							{{ $endDate->format('M d, Y') }}</p>
					</div>
					<form method="GET" class="flex gap-2 items-end">
						<select name="investor_id" onchange="this.form.submit()"
							class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">Select Investor</option>
							@foreach ($investors as $investor)
								<option value="{{ $investor->id }}" {{ request('investor_id') == $investor->id ? 'selected' : '' }}>
									{{ $investor->name }}
								</option>
							@endforeach
						</select>
						<div>
							<input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Filter
						</button>
					</form>
				</div>

				@if ($report)
					<!-- Summary Stats -->
					<div class="px-6 py-4">
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Investment</div>
								<div class="mt-1 font-bold text-blue-600 text-title-sm">{{ number_format($report['total_investment'], 2) }} BDT
								</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Total Returns</div>
								<div class="mt-1 font-bold text-green-600 text-title-sm">{{ number_format($report['total_returns'], 2) }} BDT
								</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">Net Profit</div>
								<div class="mt-1 font-bold text-purple-600 text-title-sm">{{ number_format($report['net_profit'], 2) }} BDT</div>
							</div>
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
								<div class="text-sm text-gray-500 dark:text-gray-400">ROI</div>
								<div class="mt-1 font-bold text-green-600 text-title-sm">{{ number_format($report['roi_percentage'], 1) }}%
								</div>
							</div>
						</div>
					</div>

					<!-- Investor Info -->
					@if ($selectedInvestor)
						<div class="px-6 pb-4">
							<div class="rounded-xl border border-gray-200 bg-purple-50 p-4 dark:border-purple-700/50 dark:bg-purple-500/10">
								<h4 class="font-semibold text-purple-800 dark:text-purple-200">{{ $selectedInvestor->name }}</h4>
								<p class="text-sm text-purple-600 dark:text-purple-400">{{ $selectedInvestor->email }}</p>
								<div class="mt-2 flex gap-4 text-sm">
									<span><strong>Investment:</strong> {{ number_format($report['total_investment'], 2) }} BDT</span>
									<span><strong>Ownership:</strong> {{ $report['ownership_percentage'] }}%</span>
								</div>
							</div>
						</div>
					@endif

					<!-- ROI Breakdown -->
					<div class="px-6 pb-5">
						<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-3">ROI Breakdown</h4>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
								<h5 class="font-medium text-gray-800 dark:text-white/90 mb-4">Investment Summary</h5>
								<div class="space-y-3">
									<div class="flex justify-between">
										<span class="text-sm text-gray-500 dark:text-gray-400">Opening Balance</span>
										<span
											class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($report['opening_balance'], 2) }}
											BDT</span>
									</div>
									<div class="flex justify-between">
										<span class="text-sm text-gray-500 dark:text-gray-400">Additional Investment</span>
										<span class="text-sm font-medium text-green-600">+{{ number_format($report['additional_investment'], 2) }}
											BDT</span>
									</div>
									<div class="flex justify-between">
										<span class="text-sm text-gray-500 dark:text-gray-400">Withdrawals</span>
										<span class="text-sm font-medium text-red-600">-{{ number_format($report['withdrawals'], 2) }} BDT</span>
									</div>
									<div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
										<span class="text-sm font-semibold text-gray-800 dark:text-white/90">Closing Balance</span>
										<span class="text-sm font-bold text-blue-600">{{ number_format($report['closing_balance'], 2) }} BDT</span>
									</div>
								</div>
							</div>

							<div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
								<h5 class="font-medium text-gray-800 dark:text-white/90 mb-4">Returns Distribution</h5>
								<div class="space-y-3">
									<div class="flex justify-between">
										<span class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</span>
										<span
											class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($report['total_revenue'], 2) }}
											BDT</span>
									</div>
									<div class="flex justify-between">
										<span class="text-sm text-gray-500 dark:text-gray-400">Total Costs</span>
										<span class="text-sm font-medium text-red-600">-{{ number_format($report['total_costs'], 2) }} BDT</span>
									</div>
									<div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
										<span class="text-sm font-semibold text-gray-800 dark:text-white/90">Gross Profit</span>
										<span class="text-sm font-bold text-green-600">{{ number_format($report['gross_profit'], 2) }} BDT</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				@else
					<div class="px-6 py-12 text-center">
						<svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<h4 class="text-lg font-medium text-gray-800 dark:text-white/90">Select an Investor</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose an investor to view their ROI report</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
