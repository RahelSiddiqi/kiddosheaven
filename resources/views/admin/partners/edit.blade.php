@extends('admin.layouts.app')

@section('title', 'Edit Partner — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<form action="{{ route('admin.partners.update', $partner) }}" method="POST">
					@csrf
					@method('PUT')

					<!-- Header -->
					<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Partner</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update partner information</p>
						</div>
						<div class="flex items-center gap-3">
							<a href="{{ route('admin.partners.show', $partner) }}"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
								</svg>
								Back to Details
							</a>
							<button type="submit"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
								<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								Update Partner
							</button>
						</div>
					</div>

					<!-- Form Fields -->
					<div class="grid grid-cols-12 gap-4 md:gap-6">
						<!-- Basic Information -->
						<div class="col-span-12 lg:col-span-6">
							<div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-4">Basic Information</h4>
								<div class="space-y-4">
									<div>
										<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name *</label>
										<input type="text" name="name" id="name" required value="{{ old('name', $partner->name) }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										@error('name')
											<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
										@enderror
									</div>

									<div>
										<label for="type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type *</label>
										<select name="type" id="type" required
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
											<option value="">Select Type</option>
											<option value="supplier" {{ old('type', $partner->type) == 'supplier' ? 'selected' : '' }}>Supplier</option>
											<option value="reseller" {{ old('type', $partner->type) == 'reseller' ? 'selected' : '' }}>Reseller</option>
											<option value="affiliate" {{ old('type', $partner->type) == 'affiliate' ? 'selected' : '' }}>Affiliate
											</option>
											<option value="franchise" {{ old('type', $partner->type) == 'franchise' ? 'selected' : '' }}>Franchise
											</option>
											<option value="employee" {{ old('type', $partner->type) == 'employee' ? 'selected' : '' }}>Employee</option>
											<option value="service_provider" {{ old('type', $partner->type) == 'service_provider' ? 'selected' : '' }}>
												Service Provider
											</option>
										</select>
										@error('type')
											<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
										@enderror
									</div>

									<div>
										<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
										<select name="status" id="status"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
											<option value="active" {{ old('status', $partner->status) == 'active' ? 'selected' : '' }}>Active</option>
											<option value="inactive" {{ old('status', $partner->status) == 'inactive' ? 'selected' : '' }}>Inactive
											</option>
											<option value="suspended" {{ old('status', $partner->status) == 'suspended' ? 'selected' : '' }}>Suspended
											</option>
										</select>
									</div>

									<div>
										<label for="commission_rate"
											class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Commission Rate (%)</label>
										<input type="number" name="commission_rate" id="commission_rate" step="0.01" min="0" max="100"
											value="{{ old('commission_rate', $partner->commission_rate) }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
										@error('commission_rate')
											<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
										@enderror
									</div>
								</div>
							</div>
						</div>

						<!-- Contact Information -->
						<div class="col-span-12 lg:col-span-6">
							@php
								$contactInfo = is_string($partner->contact_info)
								    ? json_decode($partner->contact_info, true) ?? []
								    : $partner->contact_info ?? [];
							@endphp
							<div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-4">Contact Information</h4>
								<div class="space-y-4">
									<div>
										<label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
										<input type="email" name="email" id="email" value="{{ old('email', $contactInfo['email'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="email@example.com">
									</div>
									<div>
										<label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
										<input type="text" name="phone" id="phone" value="{{ old('phone', $contactInfo['phone'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="+8801XXXXXXXXX">
									</div>
									<div>
										<label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
										<textarea name="address" id="address" rows="2"
										 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
										 placeholder="Full address">{{ old('address', $contactInfo['address'] ?? '') }}</textarea>
									</div>
								</div>
							</div>
						</div>

						<!-- Bank Details -->
						<div class="col-span-12 lg:col-span-6">
							@php
								$bankDetails = is_string($partner->bank_details)
								    ? json_decode($partner->bank_details, true) ?? []
								    : $partner->bank_details ?? [];
							@endphp
							<div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-4">Bank Details</h4>
								<div class="space-y-4">
									<div>
										<label for="bank_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Bank
											Name</label>
										<input type="text" name="bank_name" id="bank_name"
											value="{{ old('bank_name', $bankDetails['bank_name'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="Bank Name">
									</div>
									<div>
										<label for="account_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Account
											Number</label>
										<input type="text" name="account_number" id="account_number"
											value="{{ old('account_number', $bankDetails['account_number'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="Account Number">
									</div>
									<div>
										<label for="account_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Account
											Name</label>
										<input type="text" name="account_name" id="account_name"
											value="{{ old('account_name', $bankDetails['account_name'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="Account Holder Name">
									</div>
									<div>
										<label for="routing_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Routing
											Number</label>
										<input type="text" name="routing_number" id="routing_number"
											value="{{ old('routing_number', $bankDetails['routing_number'] ?? '') }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
											placeholder="Routing Number">
									</div>
								</div>
							</div>
						</div>

						<!-- Notes -->
						<div class="col-span-12 lg:col-span-6">
							<div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-4">Notes</h4>
								<div>
									<textarea name="notes" id="notes" rows="6"
									 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									 placeholder="Additional notes about this partner...">{{ old('notes', $partner->notes) }}</textarea>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
