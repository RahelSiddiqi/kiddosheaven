@extends('admin.layouts.app')

@section('title', 'Loyalty Program — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center gap-4">
						<div class="p-2.5 rounded-xl bg-green-100 dark:bg-green-900/30">
							<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Points Issued</p>
							<p class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_points_issued']) }}
							</p>
						</div>
					</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center gap-4">
						<div class="p-2.5 rounded-xl bg-red-100 dark:bg-red-900/30">
							<svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Points Redeemed</p>
							<p class="text-xl font-semibold text-red-600 dark:text-red-400">
								{{ number_format($stats['total_points_redeemed']) }}</p>
						</div>
					</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center gap-4">
						<div class="p-2.5 rounded-xl bg-blue-100 dark:bg-blue-900/30">
							<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
							<p class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['active_users']) }}</p>
						</div>
					</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<div class="flex items-center gap-4">
						<div
							class="p-2.5 rounded-xl {{ $stats['active_program'] ? 'bg-green-100 dark:bg-green-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30' }}">
							<svg
								class="w-6 h-6 {{ $stats['active_program'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}"
								fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
							<p
								class="text-xl font-semibold {{ $stats['active_program'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
								{{ $stats['active_program'] ? 'Active' : 'Inactive' }}
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
				<!-- Program Settings -->
				@if ($program)
					<form action="{{ route('admin.loyalty.update', $program) }}" method="POST"
						class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
						@csrf
						@method('PUT')
						<div class="mb-6">
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Program Settings</h3>
						</div>
						<div class="space-y-4">
							<div>
								<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Program
									Name</label>
								<input type="text" id="name" name="name" value="{{ old('name', $program->name) }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>

							<div>
								<label for="description"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
								<textarea id="description" name="description" rows="2"
								 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('description', $program->description) }}</textarea>
							</div>

							<div class="grid grid-cols-2 gap-4">
								<div>
									<label for="points_per_currency"
										class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Points per $1</label>
									<input type="number" id="points_per_currency" name="points_per_currency"
										value="{{ old('points_per_currency', $program->points_per_currency) }}" step="0.01"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
								</div>

								<div>
									<label for="minimum_points" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Min.
										Points to Redeem</label>
									<input type="number" id="minimum_points" name="minimum_points"
										value="{{ old('minimum_points', $program->minimum_points) }}"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
								</div>
							</div>

							<div>
								<label for="discount_percentage"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Discount Rate</label>
								<div class="flex items-center gap-2">
									<input type="number" id="discount_percentage" name="discount_percentage"
										value="{{ old('discount_percentage', $program->discount_percentage) }}" step="0.01"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
									<span class="text-sm text-gray-500 dark:text-gray-400">% discount per point</span>
								</div>
							</div>

							<div>
								<label class="flex items-center gap-2 cursor-pointer">
									<input type="checkbox" name="is_active" value="1" {{ $program->is_active ? 'checked' : '' }}
										class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800">
									<span class="text-sm text-gray-700 dark:text-gray-400">Active Program</span>
								</label>
							</div>
						</div>

						<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
							<button type="submit"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M10.0001 2.91659C6.21676 2.91659 2.91676 6.21659 2.91676 9.99993C2.91676 13.7833 6.21676 17.0833 10.0001 17.0833C13.7834 17.0833 17.0834 13.7833 17.0834 9.99993C17.0834 6.21659 13.7834 2.91659 10.0001 2.91659Z"
										stroke="currentColor" stroke-width="1.5" />
									<path d="M12.5001 10.4167L10.0001 12.9167L7.50008 10.4167" stroke="currentColor" stroke-width="1.5"
										stroke-linecap="round" stroke-linejoin="round" />
								</svg>
								Save Changes
							</button>
						</div>
					</form>
				@else
					<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
						<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<p class="text-sm font-medium text-gray-900 dark:text-white">No active loyalty program</p>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a loyalty program to start rewarding customers.
						</p>
					</div>
				@endif

				<!-- Add Points Form -->
				<form action="{{ route('admin.loyalty.add-points') }}" method="POST"
					class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					@csrf
					<div class="mb-6">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Manual Points Addition</h3>
					</div>
					<div class="space-y-4">
						<div>
							<label for="user_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Customer</label>
							<select id="user_id" name="user_id"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">Select a customer</option>
								@foreach (\App\Models\User::where('is_admin', false)->get() as $user)
									<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
								@endforeach
							</select>
						</div>

						<div>
							<label for="points" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Points to
								Add</label>
							<input type="number" id="points" name="points" min="1"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>

						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reason</label>
							<input type="text" id="description" name="description" placeholder="e.g., Bonus for review"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						</div>
					</div>

					<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Points
						</button>
					</div>
				</form>
			</div>

			<!-- Recent Transactions -->
			<div class="rounded-2xl border border-gray-200 bg-white pt-4 mt-6 dark:border-gray-800 dark:bg-white/3">
				<div class="px-5 mb-4">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Recent Transactions</h3>
				</div>
				<div class="overflow-hidden">
					<div class="max-w-full px-5 overflow-x-auto">
						<table class="min-w-full">
							<thead>
								<tr class="border-gray-200 border-y dark:border-gray-700">
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Customer</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Type</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Points</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Description</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Date</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@forelse($transactions as $transaction)
									<tr>
										<td class="py-4 whitespace-nowrap">
											@if ($transaction->user)
												<div class="font-medium text-sm text-gray-900 dark:text-white">{{ $transaction->user->name }}</div>
											@else
												<span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
											@endif
										</td>
										<td class="py-4 whitespace-nowrap">
											<span
												class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
												{{ $transaction->type === 'earned' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
												{{ $transaction->type === 'redeemed' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
												{{ $transaction->type === 'bonus' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
												{{ $transaction->type === 'expired' ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' : '' }}">
												{{ $transaction->type }}
											</span>
										</td>
										<td class="py-4 whitespace-nowrap">
											<span
												class="text-sm font-semibold {{ $transaction->isAddition() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
												{{ $transaction->isAddition() ? '+' : '-' }}{{ number_format($transaction->points) }}
											</span>
										</td>
										<td class="py-4">
											<span
												class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($transaction->description, 30) }}</span>
										</td>
										<td class="py-4 whitespace-nowrap">
											<span
												class="text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No transactions yet.</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				@if ($transactions->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
						<div class="flex items-center justify-between">
							<button @click="window.location.href='{{ $transactions->appends(request()->query())->previousPageUrl() }}'"
								{{ !$transactions->appends(request()->query())->previousPageUrl() ? 'disabled' : '' }}
								class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 sm:px-3.5 {{ !$transactions->appends(request()->query())->previousPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd"
										d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
										fill="currentColor" />
								</svg>
								<span class="hidden sm:inline">Previous</span>
							</button>

							<span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
								{{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</span>

							<ul class="hidden items-center gap-0.5 sm:flex">
								@foreach ($transactions->appends(request()->query())->links()->elements[0] as $page => $url)
									<li>
										<button @click="window.location.href='{{ $url }}'"
											class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium {{ $page == $transactions->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/8 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500' }}">
											{{ $page }}
										</button>
									</li>
								@endforeach
							</ul>

							<button @click="window.location.href='{{ $transactions->appends(request()->query())->nextPageUrl() }}'"
								{{ !$transactions->appends(request()->query())->nextPageUrl() ? 'disabled' : '' }}
								class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 sm:px-3.5 {{ !$transactions->appends(request()->query())->nextPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
								<span class="hidden sm:inline">Next</span>
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd"
										d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
										fill="currentColor" />
								</svg>
							</button>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
