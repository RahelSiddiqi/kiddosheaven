@extends('admin.layouts.app')

@section('title', 'Profit & Loss Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Filter Form -->
			<form method="GET" class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex flex-wrap items-end gap-4">
					<div class="w-40">
						<label for="from_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Date</label>
						<input type="date" name="from_date" id="from_date" value="{{ request('from_date', $fromDate) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="w-40">
						<label for="to_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To Date</label>
						<input type="date" name="to_date" id="to_date" value="{{ request('to_date', $toDate) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<button type="submit"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						Filter
					</button>
					<a href="{{ route('admin.reports.profitLoss') }}"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Reset
					</a>
				</div>
			</form>

			<!-- Summary Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
					<p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">৳{{ number_format($totalRevenue / 100, 2) }}
					</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p>
					<p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">৳{{ number_format($totalExpenses, 2) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Partner Payments</p>
					<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">৳{{ number_format($partnerPayments, 2) }}
					</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Investments</p>
					<p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">৳{{ number_format($totalInvestments, 2) }}</p>
				</div>
			</div>

			<!-- Profit/Loss Summary -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Gross Profit/Loss</h3>
					<div class="flex items-center justify-between mb-4">
						<span class="text-sm text-gray-500 dark:text-gray-400">Revenue - Expenses - Partner Payments</span>
						<span
							class="text-xl font-bold {{ $grossProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
							৳{{ number_format($grossProfit, 2) }}
						</span>
					</div>
					<div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
						<div class="h-full {{ $grossProfit >= 0 ? 'bg-green-500' : 'bg-red-500' }}"
							style="width: {{ min(100, max(0, ($grossProfit / max(1, abs($grossProfit))) * 50)) }}%"></div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Net Profit/Loss</h3>
					<div class="flex items-center justify-between mb-4">
						<span class="text-sm text-gray-500 dark:text-gray-400">Gross Profit - Investments</span>
						<span
							class="text-xl font-bold {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
							৳{{ number_format($netProfit, 2) }}
						</span>
					</div>
					<div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
						<div class="h-full {{ $netProfit >= 0 ? 'bg-green-500' : 'bg-red-500' }}"
							style="width: {{ min(100, max(0, ($netProfit / max(1, abs($netProfit))) * 50)) }}%"></div>
					</div>
				</div>
			</div>

			<!-- Expense Breakdown -->
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Expense Breakdown by Category</h3>

				@if ($expenseBreakdown->isEmpty())
					<p class="text-sm text-gray-500 dark:text-gray-400">No expenses found for the selected period.</p>
				@else
					<div class="space-y-4">
						@foreach ($expenseBreakdown as $category)
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-3">
									<span class="w-3 h-3 rounded-full" style="background-color: {{ $category['color'] ?? '#6366f1' }}"></span>
									<span class="text-sm text-gray-700 dark:text-gray-300">{{ $category['name'] }}</span>
								</div>
								<div class="flex items-center gap-4">
									<span class="text-sm text-gray-500 dark:text-gray-400">৳{{ number_format($category['amount'], 2) }}</span>
									<span class="text-sm font-medium text-gray-900 dark:text-white w-20 text-right">
										{{ $totalExpenses > 0 ? number_format(($category['amount'] / $totalExpenses) * 100, 1) : 0 }}%
									</span>
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
