@extends('admin.layouts.app')

@section('title', 'Capital Accounts — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Capital Accounts</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track partner and investor capital accounts</p>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<form method="GET" class="flex gap-2">
							<select name="partner_id" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Partners</option>
								@foreach ($partners as $partner)
									<option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
										{{ $partner->name }}
									</option>
								@endforeach
							</select>
							<select name="type" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Types</option>
								<option value="partner" {{ request('type') == 'partner' ? 'selected' : '' }}>Partner</option>
								<option value="investor" {{ request('type') == 'investor' ? 'selected' : '' }}>Investor</option>
							</select>
						</form>
					</div>
				</div>

				<!-- Stats Cards -->
				<div class="px-6 pb-4">
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Capital</div>
							<div class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
								{{ number_format($accounts->sum('current_balance'), 2) }} BDT
							</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Partners</div>
							<div class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
								{{ $accounts->where('account_type', 'partner')->count() }}
							</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Investors</div>
							<div class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
								{{ $accounts->where('account_type', 'investor')->count() }}
							</div>
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
										Partner/Investor</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Type
									</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Opening Balance</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Current Balance</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Created</th>
									<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@forelse($accounts as $account)
									<tr>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="flex items-center">
												<div class="ml-4">
													<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $account->partner->name }}</div>
													<div class="text-xs text-gray-500 dark:text-gray-400">{{ $account->partner->email }}</div>
												</div>
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<span
												class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->account_type == 'partner' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-500' : 'bg-purple-100 text-purple-600 dark:bg-purple-500/15 dark:text-purple-500' }}">
												{{ ucfirst($account->account_type) }}
											</span>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($account->opening_balance, 2) }} BDT
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm font-bold text-gray-900 dark:text-white">
												{{ number_format($account->current_balance, 2) }} BDT</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ \Carbon\Carbon::parse($account->created_at)->format('M d, Y') }}
											</div>
										</td>
										<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
											<div class="flex items-center gap-2 justify-end">
												<a href="{{ route('admin.financial-transactions.index', ['account_id' => $account->id]) }}"
													class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View Transactions">
													<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
													</svg>
												</a>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
											No capital accounts found.
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Pagination -->
				@if ($accounts->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
						{{ $accounts->appends(request()->query())->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
