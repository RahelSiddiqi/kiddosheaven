@extends('admin.layouts.app')

@section('title', 'Expense Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<x-admin.ui.entity-header title="Expense Report" :breadcrumbs="[
			    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
			    ['label' => 'Reports', 'url' => route('admin.reports.index')],
			    ['label' => 'Expenses'],
			]" />

			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
				<x-admin.ui.stat-card label="Total Approved" value="৳{{ number_format($totalAmount, 2) }}" icon="dollar"
					color="green" />
				<x-admin.ui.stat-card label="Pending Approval" value="৳{{ number_format($pendingAmount, 2) }}" icon="alert"
					color="yellow" />
				<x-admin.ui.stat-card label="Total Expenses" :value="$expenses->count()" icon="package" color="blue" />
			</div>

			<!-- Filter Form -->
			<form method="GET"
				class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 xl:flex xl:flex-nowrap items-end gap-4">
				<div class="flex-1 min-w-[160px]">
						<label for="category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
						<select name="category" id="category"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Categories</option>
							@foreach ($categories as $category)
								<option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
									{{ $category->name }}
								</option>
							@endforeach
						</select>
					</div>
				<div class="flex-1 min-w-[160px]">
						<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
						<select name="status" id="status"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Status</option>
						@foreach ($statuses as $value => $label)
							<option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
						@endforeach
						</select>
					</div>
				<div class="flex-1 min-w-[160px]">
						<label for="from_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Date</label>
						<input type="date" name="from_date" id="from_date"
							value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
				<div class="flex-1 min-w-[160px]">
						<label for="to_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To Date</label>
						<input type="date" name="to_date" id="to_date"
							value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<button type="submit"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						Filter
					</button>
					<a href="{{ route('admin.reports.expenses') }}"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Reset
					</a>
				</div>
			</form>

			@if ($expenses->isEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
					<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					<p class="text-sm font-medium text-gray-900 dark:text-white">No expenses found</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No expenses found for the selected filters.</p>
				</div>
			@else
				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Expense Report</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detailed expense breakdown</p>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Date</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Title</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Category</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Partner</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Amount</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($expenses as $expense)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-900 dark:text-white">{{ $expense->expense_date->format('M d, Y') }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm font-medium text-gray-900 dark:text-white">{{ $expense->title }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-500 dark:text-gray-400">{{ $expense->category->name }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($expense->partner)
													<a href="{{ route('admin.partners.show', $expense->partner) }}"
														class="text-sm text-blue-500 hover:underline">{{ $expense->partner->name }}</a>
												@else
													<span class="text-sm text-gray-500 dark:text-gray-400">-</span>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												<span
													class="text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($expense->amount, 2) }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400']; @endphp
												<span
													class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$expense->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
													{{ ucfirst($expense->status) }}
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
