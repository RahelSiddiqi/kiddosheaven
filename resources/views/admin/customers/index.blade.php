@extends('admin.layouts.app')

@section('title', 'Customers — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="customerManager({
    initialSearch: '{{ request('search', '') }}',
    baseUrl: '/admin/customers',
    csrf: '{{ csrf_token() }}'
})">
				<!-- Toast Notification -->
				<div x-show="toastShow" x-transition.opacity.duration.300ms
					class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
					:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'" style="display: none;">
					<svg x-show="toastType === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					<svg x-show="toastType === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
					<span x-text="toastMessage" class="text-sm font-medium"></span>
				</div>

				<!-- Stats Cards -->
				<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
					<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
						<div class="flex items-center gap-4">
							<div class="p-2.5 rounded-xl bg-blue-100 dark:bg-blue-900/30">
								<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
								</svg>
							</div>
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</p>
								<p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $customers->total() }}</p>
							</div>
						</div>
					</div>
					<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
						<div class="flex items-center gap-4">
							<div class="p-2.5 rounded-xl bg-green-100 dark:bg-green-900/30">
								<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</p>
								<p class="text-xl font-semibold text-gray-900 dark:text-white">
									{{ $customers->where('is_active', true)->count() }}</p>
							</div>
						</div>
					</div>
					<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
						<div class="flex items-center gap-4">
							<div class="p-2.5 rounded-xl bg-red-100 dark:bg-red-900/30">
								<svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactive</p>
								<p class="text-xl font-semibold text-gray-900 dark:text-white">
									{{ $customers->where('is_active', false)->count() }}</p>
							</div>
						</div>
					</div>
					<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
						<div class="flex items-center gap-4">
							<div class="p-2.5 rounded-xl bg-purple-100 dark:bg-purple-900/30">
								<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
							</div>
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">New This Month</p>
								<p class="text-xl font-semibold text-gray-900 dark:text-white">
									{{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
							</div>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Customers</h3>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<form @submit.prevent="searchCustomers()">
								<div class="relative">
									<button type="button" @click="searchCustomers()" class="absolute -translate-y-1/2 left-4 top-1/2">
										<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
												fill="" />
										</svg>
									</button>
									<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search customers..."
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-75" />
								</div>
							</form>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Customer
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Contact</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Joined Date</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@forelse ($customers as $customer)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<div class="flex items-center gap-3">
													<div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
														style="background-color: hsl(178 99% 28%);">
														{{ substr($customer->name, 0, 1) }}
													</div>
													<div>
														<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</div>
														<div class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ $customer->id }}</div>
													</div>
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $customer->email }}</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone ?? 'No phone' }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $customer->created_at->format('M d, Y') }}</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->created_at->diffForHumans() }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@if ($customer->is_active ?? true)
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
														Active
													</span>
												@else
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
														Inactive
													</span>
												@endif
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<a href="{{ route('admin.customers.show', $customer) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													<form action="{{ route('admin.customers.toggle', $customer) }}" method="POST" class="inline">
														@csrf
														<button type="submit" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
															title="{{ $customer->is_active ?? true ? 'Disable' : 'Enable' }}">
															@if ($customer->is_active ?? true)
																<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
																</svg>
															@else
																<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
																</svg>
															@endif
														</button>
													</form>
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No customers found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					<!-- Pagination -->
					@if ($customers->hasPages())
						<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
							<div class="flex items-center justify-between">
								<button @click="window.location.href='{{ $customers->appends(request()->query())->previousPageUrl() }}'"
									{{ !$customers->appends(request()->query())->previousPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$customers->appends(request()->query())->previousPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
											fill="currentColor" />
									</svg>
									<span class="hidden sm:inline">Previous</span>
								</button>

								<span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
									{{ $customers->currentPage() }} of {{ $customers->lastPage() }}</span>

								<ul class="hidden items-center gap-0.5 sm:flex">
									@foreach ($customers->appends(request()->query())->links()->elements[0] as $page => $url)
										<li>
											<button @click="window.location.href='{{ $url }}'"
												class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium {{ $page == $customers->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/8 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500' }}">
												{{ $page }}
											</button>
										</li>
									@endforeach
								</ul>

								<button @click="window.location.href='{{ $customers->appends(request()->query())->nextPageUrl() }}'"
									{{ !$customers->appends(request()->query())->nextPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$customers->appends(request()->query())->nextPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
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
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('customerManager', (config) => ({
				searchTerm: config.initialSearch,
				toastMessage: '',
				toastType: 'success',
				toastShow: false,

				showToast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.toastShow = true;
					setTimeout(() => {
						this.toastShow = false;
					}, 3000);
				},

				searchCustomers() {
					const url = new URL(window.location);
					url.searchParams.set('search', this.searchTerm);
					url.searchParams.set('page', 1);
					window.location.href = url.toString();
				},

				handleKeydown(e) {
					if (e.key === 'Enter') this.searchCustomers();
				},
			}))
		})
	</script>
@endpush
