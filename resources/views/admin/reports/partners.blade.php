@extends('admin.layouts.app')

@section('title', 'Partner Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Partners</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $partners->count() }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</p>
					<p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">৳{{ number_format($totalPaid, 2) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Payments</p>
					<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">৳{{ number_format($pendingPayments, 2) }}</p>
				</div>
			</div>

			<!-- Filter Form -->
			<form method="GET" class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex flex-wrap items-end gap-4">
					<div class="w-40">
						<label for="from_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Date</label>
						<input type="date" name="from_date" id="from_date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="w-40">
						<label for="to_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To Date</label>
						<input type="date" name="to_date" id="to_date" value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<button type="submit"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						Filter
					</button>
					<a href="{{ route('admin.reports.partners') }}"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Reset
					</a>
				</div>
			</form>

			@if ($partners->isEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
					<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					<p class="text-sm font-medium text-gray-900 dark:text-white">No partners found</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No partners found for the selected filters.</p>
				</div>
			@else
				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Partner Report</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Partner payment summary</p>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Partner</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Type</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Commission</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Total Paid</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Pending</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($partners as $partner)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<a href="{{ route('admin.partners.show', $partner) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
													{{ $partner->name }}
												</a>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($partner->type) }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-theme-xs inline-block rounded-full px-2.5 py-0.5 font-medium {{ $partner->status_badge_class }}">
													{{ ucfirst($partner->status) }}
												</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-900 dark:text-white">{{ $partner->commission_rate }}%</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm font-medium text-green-600 dark:text-green-400">
													৳{{ number_format($partner->payments->where('status', 'completed')->sum('amount'), 2) }}
												</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
													৳{{ number_format($partner->calculations->where('status', 'approved')->sum('payment_amount'), 2) }}
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
