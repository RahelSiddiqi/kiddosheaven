@extends('admin.layouts.app')

@section('title', 'Add Partner — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 lg:col-span-8">
			<form action="{{ route('admin.partners.store') }}" method="POST"
				class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf

				<!-- Header -->
				<div class="mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add New Partner</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter partner information below</p>
				</div>

				<!-- Form Fields -->
				<div class="space-y-5">
					<div>
						<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name *</label>
						<input type="text" name="name" id="name" required value="{{ old('name') }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
							placeholder="Enter partner name">
						@error('name')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div>
							<label for="type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type *</label>
							<select name="type" id="type" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">Select Type</option>
								<option value="supplier" {{ old('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
								<option value="reseller" {{ old('type') == 'reseller' ? 'selected' : '' }}>Reseller</option>
								<option value="affiliate" {{ old('type') == 'affiliate' ? 'selected' : '' }}>Affiliate</option>
								<option value="franchise" {{ old('type') == 'franchise' ? 'selected' : '' }}>Franchise</option>
								<option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>Employee</option>
								<option value="service_provider" {{ old('type') == 'service_provider' ? 'selected' : '' }}>
									Service Provider
								</option>
							</select>
							@error('type')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="commission_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Commission
								Rate (%)</label>
							<input type="number" name="commission_rate" id="commission_rate" step="0.01" min="0" max="100"
								value="{{ old('commission_rate', 0) }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
								placeholder="0.00">
							@error('commission_rate')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Contact Information -->
					<div>
						<h4 class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-3">Contact Information</h4>
						<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
							<div>
								<label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
								<input type="email" name="email" id="email" value="{{ old('email') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="email@example.com">
							</div>
							<div>
								<label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
								<input type="text" name="phone" id="phone" value="{{ old('phone') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="+8801XXXXXXXXX">
							</div>
							<div>
								<label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
								<input type="text" name="address" id="address" value="{{ old('address') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Full address">
							</div>
						</div>
					</div>

					<!-- Bank Details -->
					<div>
						<h4 class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-3">Bank Details</h4>
						<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
							<div>
								<label for="bank_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Bank
									Name</label>
								<input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Bank Name">
							</div>
							<div>
								<label for="account_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Account
									Number</label>
								<input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Account Number">
							</div>
							<div>
								<label for="account_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Account
									Name</label>
								<input type="text" name="account_name" id="account_name" value="{{ old('account_name') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Account Holder Name">
							</div>
							<div>
								<label for="routing_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Routing
									Number</label>
								<input type="text" name="routing_number" id="routing_number" value="{{ old('routing_number') }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Routing Number">
							</div>
						</div>
					</div>

					<div>
						<label for="notes" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Notes</label>
						<textarea name="notes" id="notes" rows="2" placeholder="Additional notes..."
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('notes') }}</textarea>
					</div>
				</div>

				<!-- Submit Button -->
				<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
					<div class="flex gap-4">
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Create Partner
						</button>
						<a href="{{ route('admin.partners.index') }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							Cancel
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
