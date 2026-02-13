@extends('admin.layouts.app')

@section('title', 'Financial Transactions — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
				<x-admin.ui.stat-card title="Total Credits" :value="number_format($transactions->where('transaction_type', 'credit')->sum('amount'), 2) . ' BDT'" icon="trending" color="green" />
				<x-admin.ui.stat-card title="Total Debits" :value="number_format($transactions->where('transaction_type', 'debit')->sum('amount'), 2) . ' BDT'" icon="cart" color="red" />
			</div>

			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Financial Transactions</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track all financial transactions for capital accounts</p>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<form method="GET" class="flex gap-2">
							<select name="account_id" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Accounts</option>
								@foreach ($accounts as $account)
									<option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
										{{ $account->partner->name }} ({{ ucfirst($account->account_type) }})
									</option>
								@endforeach
							</select>
							<select name="type" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Types</option>
								<option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
								<option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
							</select>
						</form>
					</div>
				</div>

				<!-- Table -->
				<div class="overflow-hidden">
					<div class="max-w-full overflow-x-auto">
						<table class="min-w-full">
							<thead>
								<tr class="border-gray-200 border-y dark:border-gray-700">
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Date
									</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Account</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Type
									</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Amount</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Description</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Reference</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@forelse($transactions as $transaction)
									<tr>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm font-medium text-gray-900 dark:text-white">
												{{ $transaction->capitalAccount->partner->name }}
											</div>
											<div class="text-xs text-gray-500 dark:text-gray-400">
												{{ ucfirst($transaction->capitalAccount->account_type) }}
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<span
												class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->transaction_type == 'credit' ? 'bg-green-100 text-green-600 dark:bg-green-500/15 dark:text-green-500' : 'bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-500' }}">
												{{ ucfirst($transaction->transaction_type) }}
											</span>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div
												class="text-sm font-bold {{ $transaction->transaction_type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
												{{ $transaction->transaction_type == 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
												BDT
											</div>
										</td>
										<td class="px-4 py-4">
											<div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
												{{ $transaction->description ?? '-' }}
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												@if ($transaction->reference_type)
													{{ $transaction->reference_type }} #{{ $transaction->reference_id }}
												@else
													-
												@endif
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
											No financial transactions found.
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Pagination -->
				@if ($transactions->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
						{{ $transactions->appends(request()->query())->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
