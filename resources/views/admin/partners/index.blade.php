@extends('admin.layouts.app')

@section('title', 'Partners — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="partnerManager({
    initialSearch: '{{ request('search', '') }}',
    storeRoute: '{{ route('admin.partners.store') }}',
    baseUrl: '/admin/partners',
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
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
					<x-admin.ui.stat-card title="Total Partners" :value="$partners->count()" icon="users" color="blue" />
					<x-admin.ui.stat-card title="Active Partners" :value="$partners->where('status', 'active')->count()" icon="cart" color="green" />
					<x-admin.ui.stat-card title="Total Commission Paid" :value="'৳ ' . number_format($partners->sum('total_commission_paid') ?? 0, 2)" icon="currency" color="purple" />
				</div>

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Partners</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage business partners and track their performance</p>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<form @submit.prevent="searchPartners()">
								<div class="relative">
									<button type="button" @click="searchPartners()" class="absolute -translate-y-1/2 left-4 top-1/2">
										<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
												fill="" />
										</svg>
									</button>
									<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search partners..."
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-75" />
								</div>
							</form>
							<button @click="openCreateModal()"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
								Add Partner
							</button>
						</div>
					</div>

					<!-- Filters -->
					<form action="{{ route('admin.partners.index') }}" method="GET" class="p-5 pt-0">
						<div class="flex flex-wrap items-end gap-3">
							<div class="flex-1 min-w-[200px]">
								<input type="text" name="search" placeholder="Search partners..." value="{{ request('search') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							</div>
							<div class="w-40">
								<select name="status"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">All Status</option>
									<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
									<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
									<option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
								</select>
							</div>
							<div class="w-40">
								<select name="type"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">All Types</option>
									<option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
									<option value="reseller" {{ request('type') == 'reseller' ? 'selected' : '' }}>Reseller</option>
									<option value="affiliate" {{ request('type') == 'affiliate' ? 'selected' : '' }}>Affiliate</option>
								</select>
							</div>
							<div class="flex gap-2">
								<button type="submit"
									class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
									</svg>
								</button>
								<a href="{{ route('admin.partners.index') }}"
									class="h-11 inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-50 dark:border-red-900 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-white/3">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</a>
							</div>
						</div>
					</form>

					<!-- Table -->
					<div class="overflow-x-auto">
						<table class="w-full min-w-[1102px]">
							<thead>
								<tr class="border-b border-gray-100 dark:border-gray-800">
									<th class="px-5 py-3 text-left">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Partner</p>
									</th>
									<th class="px-5 py-3 text-left">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Type</p>
									</th>
									<th class="px-5 py-3 text-center">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Orders</p>
									</th>
									<th class="px-5 py-3 text-right">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Commission</p>
									</th>
									<th class="px-5 py-3 text-center">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
									</th>
									<th class="px-5 py-3 text-right">
										<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p>
									</th>
								</tr>
							</thead>
							<tbody>
								@forelse($partners as $partner)
									<tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
										<td class="px-5 py-4">
											<div class="flex items-center gap-3">
												<div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
													<span class="font-medium text-gray-600 dark:text-gray-300">
														{{ strtoupper(substr($partner->name, 0, 2)) }}
													</span>
												</div>
												<div>
													<p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $partner->name }}</p>
													@php
														$contactInfo = is_string($partner->contact_info)
														    ? json_decode($partner->contact_info, true)
														    : $partner->contact_info ?? [];
													@endphp
													@if (!empty($contactInfo['email']))
														<p class="text-xs text-gray-500 dark:text-gray-400">{{ $contactInfo['email'] }}</p>
													@endif
												</div>
											</div>
										</td>
										<td class="px-5 py-4">
											<span
												class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
												{{ ucfirst($partner->type) }}
											</span>
										</td>
										<td class="px-5 py-4 text-center">
											<p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $partner->total_orders ?? 0 }}</p>
										</td>
										<td class="px-5 py-4 text-right">
											<p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
												{{ number_format($partner->total_commission_paid ?? 0, 2) }}</p>
										</td>
										<td class="px-5 py-4 text-center">
											<span
												class="text-theme-xs inline-block rounded-full px-2.5 py-0.5 font-medium {{ $partner->status_badge_class }}">
												{{ ucfirst($partner->status) }}
											</span>
										</td>
										<td class="px-5 py-4 text-right">
											<div class="flex items-center justify-end gap-1">
												<a href="{{ route('admin.partners.show', $partner) }}"
													class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
													</svg>
												</a>
												<button @click="openEditModal({{ json_encode($partner) }})"
													class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
													</svg>
												</button>
												<button @click="openDeleteConfirm({{ $partner->id }}, '{{ addslashes($partner->name) }}')"
													class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
													</svg>
												</button>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="px-5 py-8 text-center">
											<p class="text-gray-500 dark:text-gray-400">No partners found</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="p-5 border-t border-gray-100 dark:border-gray-800">
						{{ $partners->appends(request()->query())->links() }}
					</div>
				</div>

				<!-- Create/Edit Modal -->
				<div x-show="showModal" x-cloak class="fixed inset-0 z-10000 overflow-y-auto">
					<div class="flex min-h-screen items-center justify-center p-4">
						<div x-show="showModal" @click="closeModal()" class="fixed inset-0 bg-black/50 transition-opacity z-10000">
						</div>
						<div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-10001"
							x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
							x-transition:enter-end="opacity-100 scale-100">
							<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
								<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90"
									x-text="modalMode === 'create' ? 'Add New Partner' : 'Edit Partner'"></h3>
								<button @click="closeModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
									<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</button>
							</div>
							<div class="p-5">
								<form @submit.prevent="submitForm()">
									<div class="mb-4">
										<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
										<input type="text" x-model="formData.name" required
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
											placeholder="Enter partner name">
									</div>
									<div class="grid grid-cols-2 gap-4 mb-4">
										<div>
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
											<input type="email" x-model="formData.email"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
												placeholder="Enter email address">
										</div>
										<div>
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
											<input type="text" x-model="formData.phone"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
												placeholder="Enter phone number">
										</div>
									</div>
									<div class="mb-4">
										<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
										<textarea x-model="formData.address" rows="2"
										 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
										 placeholder="Enter address"></textarea>
									</div>
									<div class="grid grid-cols-2 gap-4 mb-4">
										<div>
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
											<div class="relative">
												<select x-model="formData.type" required
													class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 appearance-none">
													<option value="">Select Type</option>
													<option value="supplier">Supplier</option>
													<option value="reseller">Reseller</option>
													<option value="affiliate">Affiliate</option>
													<option value="franchise">Franchise</option>
													<option value="employee">Employee</option>
													<option value="service_provider">Service Provider</option>
												</select>
												<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
													</svg>
												</div>
											</div>
										</div>
										<div>
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Commission Rate (%)</label>
											<input type="number" x-model="formData.commission_rate" step="0.01" min="0" max="100"
												class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
												placeholder="0.00">
										</div>
									</div>
									<div class="mb-4">
										<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
										<textarea x-model="formData.notes" rows="2"
										 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
										 placeholder="Enter notes"></textarea>
									</div>
									<div class="flex justify-end gap-3 mt-6">
										<button type="button" @click="closeModal()"
											class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
											Cancel
										</button>
										<button type="submit"
											class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
											<span x-text="modalMode === 'create' ? 'Create' : 'Update'"></span>
										</button>
									</div>
								</form>
							</div>
						</div>
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
							<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">Delete Partner</h3>
							<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
								Are you sure you want to delete <strong x-text="deletePartnerName"
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
	</div>

	@push('scripts')
		<script>
			document.addEventListener('alpine:init', () => {
				Alpine.data('partnerManager', (config) => ({
					searchTerm: config.initialSearch,
					showModal: false,
					modalMode: 'create',
					editingPartner: null,
					formData: {
						name: '',
						email: '',
						phone: '',
						address: '',
						type: '',
						commission_rate: '',
						notes: '',
					},
					toastMessage: '',
					toastType: 'success',
					toastShow: false,
					showDeleteModal: false,
					deletePartnerId: null,
					deletePartnerName: '',
					isDeleting: false,

					showToast(message, type = 'success') {
						this.toastMessage = message;
						this.toastType = type;
						this.toastShow = true;
						setTimeout(() => {
							this.toastShow = false;
						}, 3000);
					},

					openCreateModal() {
						this.modalMode = 'create';
						this.formData = {
							name: '',
							email: '',
							phone: '',
							address: '',
							type: '',
							commission_rate: '',
							notes: '',
						};
						this.showModal = true;
					},

					openEditModal(partner) {
						this.modalMode = 'edit';
						this.editingPartner = partner;
						// Parse contact_info to extract fields
						let contactInfo = {
							email: '',
							phone: '',
							address: ''
						};
						if (partner.contact_info) {
							if (typeof partner.contact_info === 'string') {
								contactInfo = JSON.parse(partner.contact_info);
							} else {
								contactInfo = partner.contact_info;
							}
						}
						this.formData = {
							name: partner.name,
							email: contactInfo.email || '',
							phone: contactInfo.phone || '',
							address: contactInfo.address || '',
							type: partner.type,
							commission_rate: partner.commission_rate || '',
							notes: partner.notes || '',
						};
						this.showModal = true;
					},

					closeModal() {
						this.showModal = false;
						this.editingPartner = null;
						this.formData = {
							name: '',
							email: '',
							phone: '',
							address: '',
							type: '',
							commission_rate: '',
							notes: '',
						};
					},

					submitForm() {
						this.modalMode === 'create' ? this.createPartner() : this.updatePartner();
					},

					async createPartner() {
						try {
							const formData = new FormData();
							formData.append('name', this.formData.name);
							formData.append('email', this.formData.email);
							formData.append('phone', this.formData.phone);
							formData.append('address', this.formData.address);
							formData.append('type', this.formData.type);
							formData.append('commission_rate', this.formData.commission_rate || 0);
							formData.append('notes', this.formData.notes);
							formData.append('_token', config.csrf);

							const response = await fetch(config.storeRoute, {
								method: 'POST',
								headers: {
									'X-Requested-With': 'XMLHttpRequest'
								},
								body: formData
							});

							const data = await response.json();
							if (data.success) {
								this.showToast(data.message || 'Partner created successfully!');
								this.closeModal();
								setTimeout(() => {
									window.location.reload();
								}, 1500);
							} else {
								this.showToast(data.message || 'Error creating partner', 'error');
							}
						} catch (error) {
							console.error('Error:', error);
							this.showToast('Error creating partner', 'error');
						}
					},

					async updatePartner() {
						try {
							const formData = new FormData();
							formData.append('name', this.formData.name);
							formData.append('email', this.formData.email);
							formData.append('phone', this.formData.phone);
							formData.append('address', this.formData.address);
							formData.append('type', this.formData.type);
							formData.append('commission_rate', this.formData.commission_rate || 0);
							formData.append('notes', this.formData.notes);
							formData.append('_token', config.csrf);
							formData.append('_method', 'PUT');

							const response = await fetch(`${config.baseUrl}/${this.editingPartner.id}`, {
								method: 'POST',
								headers: {
									'X-Requested-With': 'XMLHttpRequest'
								},
								body: formData
							});

							const data = await response.json();
							if (data.success) {
								this.showToast(data.message || 'Partner updated successfully!');
								this.closeModal();
								setTimeout(() => {
									window.location.reload();
								}, 1500);
							} else {
								this.showToast(data.message || 'Error updating partner', 'error');
							}
						} catch (error) {
							console.error('Error:', error);
							this.showToast('Error updating partner', 'error');
						}
					},

					openDeleteConfirm(id, name) {
						this.deletePartnerId = id;
						this.deletePartnerName = name;
						this.showDeleteModal = true;
					},

					async confirmDelete() {
						if (!this.deletePartnerId) return;
						this.isDeleting = true;
						try {
							const formData = new FormData();
							formData.append('_method', 'DELETE');
							formData.append('_token', config.csrf);

							const response = await fetch(`${config.baseUrl}/${this.deletePartnerId}`, {
								method: 'POST',
								headers: {
									'X-Requested-With': 'XMLHttpRequest'
								},
								body: formData
							});

							const data = await response.json();
							if (data.success) {
								this.showToast(data.message || 'Partner deleted successfully!');
								this.showDeleteModal = false;
								this.isDeleting = false;
								setTimeout(() => {
									window.location.reload();
								}, 1500);
							} else {
								this.showToast(data.message || 'Error deleting partner', 'error');
								this.isDeleting = false;
							}
						} catch (error) {
							console.error('Error:', error);
							this.showToast('Error deleting partner', 'error');
							this.isDeleting = false;
							this.showDeleteModal = false;
						}
					},

					searchPartners() {
						const url = new URL(window.location);
						url.searchParams.set('search', this.searchTerm);
						url.searchParams.set('page', 1);
						window.location.href = url.toString();
					},

					handleKeydown(e) {
						if (e.key === 'Enter') this.searchPartners();
					}
				}))
			})
		</script>
	@endpush
@endsection
