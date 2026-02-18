@extends('admin.layouts.app')

@section('title', 'Coupons — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="couponManager({
    initialSearch: '{{ request('search', '') }}',
    baseUrl: '/admin/coupons',
    usersUrl: '{{ route('admin.marketing.coupons.users') }}',
    csrf: '{{ csrf_token() }}',
    initialUsers: @json($users ?? [])
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
					<x-admin.ui.stat-card title="Total Coupons" :value="$coupons->total()" icon="tag" color="blue" />
					<x-admin.ui.stat-card title="Active" :value="$coupons->filter(fn($c) => $c->isValid())->count()" icon="check" color="green" />
					<x-admin.ui.stat-card title="Inactive" :value="$coupons->filter(fn($c) => !$c->isValid())->count()" icon="x-circle" color="red" />
					<x-admin.ui.stat-card title="Expiring Soon" :value="$coupons
					    ->filter(fn($c) => $c->valid_until && $c->valid_until->isBetween(now(), now()->addDays(7)))
					    ->count()" icon="clock" color="purple" />
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Coupons</h3>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<form @submit.prevent="searchCoupons()">
								<div class="relative">
									<button type="button" @click="searchCoupons()" class="absolute -translate-y-1/2 left-4 top-1/2">
										<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
												fill="" />
										</svg>
									</button>
									<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search coupons..."
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-64" />
								</div>
							</form>
							<button @click="openCouponModal()"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
								Add Coupon
							</button>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Code</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Description</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Type</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Value</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Usage</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Valid Until</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Type</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@forelse ($coupons as $coupon)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-sm font-mono">{{ $coupon->code }}</code>
											</td>
											<td class="px-4 py-4">
												<div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">{{ $coupon->description ?? '-' }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@switch($coupon->type)
													@case('percentage')
														<span
															class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Percentage</span>
													@break

													@case('fixed')
														<span
															class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Fixed</span>
													@break

													@case('shipping')
														<span
															class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Free
															Shipping</span>
													@break
												@endswitch
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-semibold text-gray-900 dark:text-white">
													@if ($coupon->type === 'percentage')
														{{ $coupon->value }}%
													@elseif($coupon->type === 'fixed')
														৳ {{ number_format($coupon->value, 0) }}
													@else
														Free
													@endif
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $coupon->used_count }}
													@if ($coupon->usage_limit)
														<span class="text-gray-500">/ {{ $coupon->usage_limit }}</span>
													@endif
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">
													{{ $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : 'No limit' }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@if ($coupon->is_general)
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">General</span>
												@else
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
														{{ $coupon->user ? 'User: ' . $coupon->user->name : 'Specific' }}
													</span>
												@endif
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@if ($coupon->isValid())
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
												@else
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
												@endif
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<button @click="editCoupon({{ $coupon }})"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.edit />
													</button>
													<button @click="openDeleteConfirm({{ $coupon->id }}, '{{ addslashes($coupon->code) }}')"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.delete />
													</button>
												</div>
											</td>
										</tr>
										@empty
											<tr>
												<td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No coupons found</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>

						<!-- Pagination -->
						@if ($coupons->hasPages())
							<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
								<div class="flex items-center justify-between">
									<button @click="window.location.href='{{ $coupons->appends(request()->query())->previousPageUrl() }}'"
										{{ !$coupons->appends(request()->query())->previousPageUrl() ? 'disabled' : '' }}
										class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$coupons->appends(request()->query())->previousPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
												fill="currentColor" />
										</svg>
										<span class="hidden sm:inline">Previous</span>
									</button>

									<span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
										{{ $coupons->currentPage() }} of {{ $coupons->lastPage() }}</span>

									<ul class="hidden items-center gap-0.5 sm:flex">
										@foreach ($coupons->appends(request()->query())->links()->elements[0] as $page => $url)
											<li>
												<button @click="window.location.href='{{ $url }}'"
													class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium {{ $page == $coupons->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/8 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500' }}">
													{{ $page }}
												</button>
											</li>
										@endforeach
									</ul>

									<button @click="window.location.href='{{ $coupons->appends(request()->query())->nextPageUrl() }}'"
										{{ !$coupons->appends(request()->query())->nextPageUrl() ? 'disabled' : '' }}
										class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$coupons->appends(request()->query())->nextPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
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

					<!-- Coupon Modal -->
					<div x-show="showModal" x-transition.opacity.duration.300ms style="display: none;"
						class="fixed inset-0 z-99999 flex items-center justify-center px-4" x-cloak>
						<div class="fixed inset-0 bg-black/50" @click="closeCouponModal()"></div>
						<div
							class="relative z-10 w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
							@click.stop>
							<div class="flex items-center justify-between mb-6">
								<h3 x-text="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Add Coupon</h3>
								<button @click="closeCouponModal()"
									class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</button>
							</div>

							<form id="couponForm" @submit.prevent="saveCoupon()">
								<div class="space-y-4">
									<div>
										<label for="code" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Coupon Code
											*</label>
										<input type="text" id="code" x-model="formData.code" required placeholder="e.g., SUMMER20"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											style="text-transform: uppercase;">
									</div>

									<div>
										<label for="description"
											class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
										<textarea id="description" x-model="formData.description" rows="2"
										 placeholder="Optional description for this coupon"
										 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"></textarea>
									</div>

									<div class="grid grid-cols-2 gap-4">
										<div>
											<label for="type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type
												*</label>
											<select id="type" x-model="formData.type" required
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
												<option value="percentage">Percentage</option>
												<option value="fixed">Fixed Amount</option>
												<option value="shipping">Free Shipping</option>
											</select>
										</div>
										<div>
											<label for="value" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Value
												*</label>
											<input type="number" id="value" x-model="formData.value" required placeholder="10" min="0"
												step="0.01"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										</div>
									</div>

									<div class="grid grid-cols-2 gap-4">
										<div>
											<label for="min_order_amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Min
												Order (৳)</label>
											<input type="number" id="min_order_amount" x-model="formData.min_order_amount" placeholder="0"
												min="0" step="0.01"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										</div>
										<div>
											<label for="max_discount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Max
												Discount (৳)</label>
											<input type="number" id="max_discount" x-model="formData.max_discount" placeholder="No limit"
												min="0" step="0.01"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										</div>
									</div>

									<div class="grid grid-cols-2 gap-4">
										<div>
											<label for="usage_limit" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Usage
												Limit</label>
											<input type="number" id="usage_limit" x-model="formData.usage_limit" placeholder="Unlimited"
												min="1"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										</div>
										<div>
											<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status
												*</label>
											<select id="status" x-model="formData.status" required
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
												<option value="active">Active</option>
												<option value="inactive">Inactive</option>
											</select>
										</div>
									</div>

									<div class="grid grid-cols-2 gap-4">
										<div>
											<label for="valid_from" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Valid
												From</label>
											<input type="date" id="valid_from" x-model="formData.valid_from"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
										</div>
										<div>
											<label for="valid_until" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Valid
												Until</label>
											<input type="date" id="valid_until" x-model="formData.valid_until"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
										</div>
									</div>

									<!-- Coupon Type -->
									<div class="border-t border-gray-200 dark:border-gray-700 pt-4">
										<h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Coupon Type</h4>
										<div class="flex flex-col gap-3">
											<label class="flex items-center gap-3 cursor-pointer">
												<input type="radio" x-model="formData.is_general" value="1"
													class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
												<div class="flex flex-col">
													<span class="text-sm font-medium text-gray-700 dark:text-gray-300">General Coupon</span>
													<span class="text-xs text-gray-500 dark:text-gray-400">Anyone can use this coupon</span>
												</div>
											</label>
											<label class="flex items-center gap-3 cursor-pointer">
												<input type="radio" x-model="formData.is_general" value="0"
													class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
												<div class="flex flex-col">
													<span class="text-sm font-medium text-gray-700 dark:text-gray-300">User-Specific Coupon</span>
													<span class="text-xs text-gray-500 dark:text-gray-400">Only specific user can use</span>
												</div>
											</label>
										</div>
									</div>

									<!-- User Selection (shown when user-specific is selected) -->
									<div x-show="formData.is_general == '0'" x-transition
										class="border-t border-gray-200 dark:border-gray-700 pt-4">
										<label for="user_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Select
											User</label>
										<div class="relative">
											<input type="text" id="user_search" x-model="userSearch" @input.debounce.300ms="searchUsers()"
												placeholder="Search by name or email..."
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
											<div x-show="loadingUsers" class="absolute right-3 top-1/2 -translate-y-1/2">
												<svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
													<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
													</circle>
													<path class="opacity-75" fill="currentColor"
														d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
													</path>
												</svg>
											</div>
										</div>
										<!-- User dropdown -->
										<div x-show="userResults.length > 0"
											class="absolute z-10 w-full max-w-md mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto">
											<template x-for="user in userResults" :key="user.id">
												<div @click="selectUser(user)" class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
													<div class="text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></div>
													<div class="text-xs text-gray-500 dark:text-gray-400" x-text="user.email"></div>
												</div>
											</template>
										</div>
										<!-- Selected user -->
										<div x-show="selectedUser" class="mt-2 flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
											<div class="flex-1">
												<div class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedUser?.name"></div>
												<div class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedUser?.email"></div>
											</div>
											<button type="button" @click="clearUser()"
												class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
												</svg>
											</button>
										</div>
									</div>
								</div>

								<div class="flex justify-end gap-3 mt-6">
									<button type="button" @click="closeCouponModal()"
										class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">Cancel</button>
									<button type="submit"
										class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
										</svg>
										<span class="ml-2">Save Coupon</span>
									</button>
								</div>
							</form>
						</div>
					</div>

					<!-- Delete Confirmation Modal -->
					<div x-show="showDeleteModal" x-transition.opacity.duration.300ms
						class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50" style="display: none;">
						<div @click.away="showDeleteModal = false" x-transition:enter="transition ease-out duration-300"
							x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
							x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
							x-transition:leave-end="opacity-0 scale-95"
							class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
							<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
								<svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							</div>
							<div class="text-center">
								<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">Delete Coupon</h3>
								<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
									Are you sure you want to delete <strong x-text="deleteCouponCode"
										class="text-gray-700 dark:text-gray-200"></strong>?
									This action cannot be undone.
								</p>
								<div class="flex gap-3">
									<button @click="showDeleteModal = false"
										class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
										Cancel
									</button>
									<button @click="confirmDelete()" :disabled="isDeleting"
										class="flex-1 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed">
										<span x-show="!isDeleting">Delete</span>
										<span x-show="isDeleting" class="flex items-center justify-center gap-2">
											<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
												<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
												</circle>
												<path class="opacity-75" fill="currentColor"
													d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
												</path>
											</svg>
											Deleting...
										</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endsection

		@push('scripts')
			<script>
				document.addEventListener('alpine:init', () => {
					Alpine.data('couponManager', (config) => ({
						searchTerm: config.initialSearch,
						showModal: false,
						modalTitle: 'Add Coupon',
						isEditing: false,
						toastMessage: '',
						toastType: 'success',
						toastShow: false,
						showDeleteModal: false,
						deleteCouponId: null,
						deleteCouponCode: '',
						isDeleting: false,
						userSearch: '',
						userResults: [],
						selectedUser: null,
						loadingUsers: false,
						usersUrl: config.usersUrl,
						formData: {
							id: null,
							code: '',
							description: '',
							type: 'percentage',
							value: '',
							min_order_amount: '',
							max_discount: '',
							usage_limit: '',
							status: 'active',
							valid_from: '',
							valid_until: '',
							is_general: '1',
							user_id: null,
						},

						showToast(message, type = 'success') {
							this.toastMessage = message;
							this.toastType = type;
							this.toastShow = true;
							setTimeout(() => {
								this.toastShow = false;
							}, 3000);
						},

						searchCoupons() {
							const url = new URL(window.location);
							url.searchParams.set('search', this.searchTerm);
							url.searchParams.set('page', 1);
							window.location.href = url.toString();
						},

						handleKeydown(e) {
							if (e.key === 'Enter') this.searchCoupons();
						},

						openCouponModal() {
							this.showModal = true;
							this.modalTitle = 'Add Coupon';
							this.isEditing = false;
							this.resetForm();
						},

						async editCoupon(coupon) {
							this.showModal = true;
							this.modalTitle = 'Edit Coupon';
							this.isEditing = true;
							this.selectedUser = coupon.user_id ? {
								id: coupon.user_id,
								name: coupon.user?.name || '',
								email: coupon.user?.email || ''
							} : null;
							this.formData = {
								id: coupon.id,
								code: coupon.code,
								description: coupon.description || '',
								type: coupon.type,
								value: coupon.value,
								min_order_amount: coupon.min_order_amount || '',
								max_discount: coupon.max_discount || '',
								usage_limit: coupon.usage_limit || '',
								status: coupon.status,
								valid_from: coupon.valid_from ? coupon.valid_from.split(' ')[0] : '',
								valid_until: coupon.valid_until ? coupon.valid_until.split(' ')[0] : '',
								is_general: coupon.is_general ? '1' : '0',
								user_id: coupon.user_id,
							};
						},

						closeCouponModal() {
							this.showModal = false;
							this.resetForm();
						},

						resetForm() {
							this.formData = {
								id: null,
								code: '',
								description: '',
								type: 'percentage',
								value: '',
								min_order_amount: '',
								max_discount: '',
								usage_limit: '',
								status: 'active',
								valid_from: '',
								valid_until: '',
								is_general: '1',
								user_id: null,
							};
							this.selectedUser = null;
							this.userSearch = '';
							this.userResults = [];
						},

						async searchUsers() {
							if (this.userSearch.length < 2) {
								this.userResults = [];
								return;
							}
							this.loadingUsers = true;
							try {
								const response = await fetch(
									`${this.usersUrl}?search=${encodeURIComponent(this.userSearch)}`);
								this.userResults = await response.json();
							} catch (error) {
								console.error('Error fetching users:', error);
								this.userResults = [];
							}
							this.loadingUsers = false;
						},

						selectUser(user) {
							this.selectedUser = user;
							this.formData.user_id = user.id;
							this.userSearch = '';
							this.userResults = [];
						},

						clearUser() {
							this.selectedUser = null;
							this.formData.user_id = null;
						},

						async saveCoupon() {
							try {
								const url = this.isEditing ? `${config.baseUrl}/${this.formData.id}` : config
									.baseUrl;
								const method = this.isEditing ? 'PUT' : 'POST';

								const response = await fetch(url, {
									method: method,
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': config.csrf,
										'X-Requested-With': 'XMLHttpRequest'
									},
									body: JSON.stringify(this.formData),
								});

								const data = await response.json();
								if (data.success) {
									this.showToast(data.message || (this.isEditing ?
										'Coupon updated successfully!' : 'Coupon created successfully!'
									));
									this.closeCouponModal();
									setTimeout(() => {
										window.location.reload();
									}, 1500);
								} else {
									this.showToast(data.message || 'An error occurred', 'error');
								}
							} catch (error) {
								this.showToast('An error occurred', 'error');
							}
						},

						openDeleteConfirm(id, code) {
							this.deleteCouponId = id;
							this.deleteCouponCode = code;
							this.showDeleteModal = true;
						},

						async confirmDelete() {
							if (!this.deleteCouponId) return;
							this.isDeleting = true;
							try {
								const response = await fetch(`${config.baseUrl}/${this.deleteCouponId}`, {
									method: 'DELETE',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': config.csrf,
										'X-Requested-With': 'XMLHttpRequest'
									},
								});
								const data = await response.json();
								if (data.success) {
									this.showToast(data.message || 'Coupon deleted successfully!');
									this.showDeleteModal = false;
									this.isDeleting = false;
									setTimeout(() => {
										window.location.reload();
									}, 1500);
								} else {
									this.showToast(data.message || 'An error occurred', 'error');
									this.isDeleting = false;
								}
							} catch (error) {
								this.showToast('An error occurred', 'error');
								this.isDeleting = false;
								this.showDeleteModal = false;
							}
						},
					}))
				})
			</script>
		@endpush
