@extends('admin.layouts.app')

@section('title', 'Reports — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<x-admin.ui.entity-header title="Reports" :breadcrumbs="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Reports']]" />

			<!-- Stats Summary -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<x-admin.ui.stat-card label="Total Expenses" value="৳{{ number_format($stats['total_expenses'], 2) }}"
					icon="trending-down" color="red" url="{{ route('admin.reports.expenses') }}" />
				<x-admin.ui.stat-card label="Total Investments" value="৳{{ number_format($stats['total_investments'], 2) }}"
					icon="dollar" color="blue" />
				<x-admin.ui.stat-card label="Partner Payouts" value="৳{{ number_format($stats['total_partner_payouts'], 2) }}"
					icon="users" color="purple" url="{{ route('admin.reports.partners') }}" />
				<x-admin.ui.stat-card label="Net Total" value="৳{{ number_format($stats['net_total'], 2) }}" icon="chart"
					color="green" />
			</div>
		</div>

		<!-- Report Tabs -->
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Tabs Header -->
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Financial Reports</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comprehensive financial overview</p>
					</div>
					<div class="flex gap-2">
						<a href="{{ route('admin.reports.expenses') }}"
							class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.expenses') ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							Expenses
						</a>
						<a href="{{ route('admin.reports.partners') }}"
							class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.partners') ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							Partners
						</a>
						<a href="{{ route('admin.reports.investments') }}"
							class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.investments') ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							Investments
						</a>
						<a href="{{ route('admin.reports.profit-loss') }}"
							class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.profit-loss') ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							P&L
						</a>
						<a href="{{ route('admin.reports.cost-history') }}"
							class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.cost-history') ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							Cost History
						</a>
					</div>
				</div>

				<!-- Date Filter -->
				<form action="{{ route('admin.reports.index') }}" method="GET"
					class="p-5 border-b border-gray-100 dark:border-gray-800">
					<div class="flex flex-wrap gap-4 items-end">
						<div>
							<label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Start
								Date</label>
							<input type="date" name="start_date" id="start_date" value="{{ request('start_date', $startDate) }}"
								class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">End Date</label>
							<input type="date" name="end_date" id="end_date" value="{{ request('end_date', $endDate) }}"
								class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<button type="submit"
								class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
								Apply Filter
							</button>
						</div>
						<div>
							<a href="{{ route('admin.reports.index') }}"
								class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								Reset
							</a>
						</div>
					</div>
				</form>

				<!-- Content -->
				<div class="p-5">
					<div class="text-center py-12">
						<svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
						<h4 class="text-lg font-medium text-gray-800 dark:text-white/90">Select a Report</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a report type from the tabs above to view detailed
							financial data</p>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
