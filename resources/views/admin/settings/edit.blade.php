@extends('admin.layouts.app')

@section('title', 'Settings — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 lg:col-span-8">
			<form action="{{ route('admin.settings.update') }}" method="POST"
				class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf
				@method('PUT')

				<!-- Header -->
				<div class="mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Settings</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your store settings and preferences.</p>
				</div>

				<!-- General Information -->
				<div class="mb-8">
					<h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">General Information</h4>
					<div class="grid gap-4 md:grid-cols-2">
						<div>
							<label for="site_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Site Name
								*</label>
							<input type="text" name="site_name" id="site_name" required
								value="{{ old('site_name', $settings['site_name'] ?? "Kiddo's Heaven") }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('site_name')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Currency *</label>
							<select name="currency" id="currency"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="USD" {{ old('currency', $settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD ($)
								</option>
								<option value="EUR" {{ old('currency', $settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€)
								</option>
								<option value="GBP" {{ old('currency', $settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP (£)
								</option>
								<option value="BDT" {{ old('currency', $settings['currency'] ?? '') === 'BDT' ? 'selected' : '' }}>BDT (৳)
								</option>
							</select>
						</div>
					</div>

					<div class="grid gap-4 md:grid-cols-2 mt-4">
						<div>
							<label for="site_email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Contact Email
								*</label>
							<input type="email" name="site_email" id="site_email" required
								value="{{ old('site_email', $settings['site_email'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('site_email')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="site_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
							<input type="text" name="site_phone" id="site_phone"
								value="{{ old('site_phone', $settings['site_phone'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('site_phone')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="mt-4">
						<label for="site_address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
						<textarea name="site_address" id="site_address" rows="2"
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
						@error('site_address')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="mt-4">
						<label for="site_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Site
							Description</label>
						<textarea name="site_description" id="site_description" rows="3"
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
						@error('site_description')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<!-- Commerce Settings -->
				<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
					<h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Commerce Settings</h4>
					<div class="grid gap-4 md:grid-cols-2">
						<div>
							<label for="tax_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tax Rate
								(%)</label>
							<input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100"
								value="{{ old('tax_rate', $settings['tax_rate'] ?? '0') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('tax_rate')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label for="free_shipping_threshold"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Free Shipping Threshold ($)</label>
							<input type="number" name="free_shipping_threshold" id="free_shipping_threshold" step="0.01" min="0"
								value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '50') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('free_shipping_threshold')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Commerce — Extra Settings -->
				<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
					<h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Commerce — Badges &amp; Policy</h4>
					<div class="grid gap-4 md:grid-cols-2">
						<div>
							<label for="return_policy_days" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Return Policy (days)</label>
							<input type="number" name="return_policy_days" id="return_policy_days" min="0"
								value="{{ old('return_policy_days', $settings['return_policy_days'] ?? '30') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="delivery_days" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Delivery Days</label>
							<input type="text" name="delivery_days" id="delivery_days" placeholder="e.g. 2-5 business days"
								value="{{ old('delivery_days', $settings['delivery_days'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div class="flex items-center gap-3">
							<input type="hidden" name="cod_enabled" value="0">
							<input type="checkbox" name="cod_enabled" id="cod_enabled" value="1"
								{{ old('cod_enabled', $settings['cod_enabled'] ?? '1') == '1' ? 'checked' : '' }}
								class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
							<label for="cod_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-400">Cash on Delivery Enabled</label>
						</div>
						<div class="flex items-center gap-3">
							<input type="hidden" name="safe_non_toxic" value="0">
							<input type="checkbox" name="safe_non_toxic" id="safe_non_toxic" value="1"
								{{ old('safe_non_toxic', $settings['safe_non_toxic'] ?? '1') == '1' ? 'checked' : '' }}
								class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
							<label for="safe_non_toxic" class="text-sm font-medium text-gray-700 dark:text-gray-400">Show «Safe &amp; Non-Toxic» Badge</label>
						</div>
					</div>
				</div>

				<!-- About Page Settings -->
				<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
					<h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">About Page</h4>
					<div class="grid gap-4 md:grid-cols-3">
						<div>
							<label for="about_founded_year" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Founded Year</label>
							<input type="text" name="about_founded_year" id="about_founded_year" placeholder="2020"
								value="{{ old('about_founded_year', $settings['about_founded_year'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="about_happy_customers" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Happy Customers Stat</label>
							<input type="text" name="about_happy_customers" id="about_happy_customers" placeholder="50K+"
								value="{{ old('about_happy_customers', $settings['about_happy_customers'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="about_products_count" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Products Count Stat</label>
							<input type="text" name="about_products_count" id="about_products_count" placeholder="2K+"
								value="{{ old('about_products_count', $settings['about_products_count'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="about_rating" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rating Stat</label>
							<input type="text" name="about_rating" id="about_rating" placeholder="5"
								value="{{ old('about_rating', $settings['about_rating'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="about_years_experience" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Years of Experience</label>
							<input type="text" name="about_years_experience" id="about_years_experience" placeholder="5+"
								value="{{ old('about_years_experience', $settings['about_years_experience'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
					</div>
				</div>

				<!-- Contact & Social Settings -->
				<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
					<h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Contact &amp; Social</h4>
					<div class="grid gap-4 md:grid-cols-2">
						<div>
							<label for="contact_hours_weekday" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weekday Hours Label</label>
							<input type="text" name="contact_hours_weekday" id="contact_hours_weekday" placeholder="Saturday - Thursday"
								value="{{ old('contact_hours_weekday', $settings['contact_hours_weekday'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="contact_hours_weekday_time" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weekday Opening Hours</label>
							<input type="text" name="contact_hours_weekday_time" id="contact_hours_weekday_time" placeholder="10:00 AM - 8:00 PM"
								value="{{ old('contact_hours_weekday_time', $settings['contact_hours_weekday_time'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="contact_hours_friday_time" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Friday Opening Hours</label>
							<input type="text" name="contact_hours_friday_time" id="contact_hours_friday_time" placeholder="2:00 PM - 8:00 PM"
								value="{{ old('contact_hours_friday_time', $settings['contact_hours_friday_time'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="social_facebook" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Facebook URL</label>
							<input type="url" name="social_facebook" id="social_facebook" placeholder="https://facebook.com/..."
								value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
						<div>
							<label for="social_instagram" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Instagram URL</label>
							<input type="url" name="social_instagram" id="social_instagram" placeholder="https://instagram.com/..."
								value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						</div>
					</div>
				</div>

				<!-- Submit Button -->
				<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
					<button type="submit"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path
								d="M10.0001 2.91659C6.21676 2.91659 2.91676 6.21659 2.91676 9.99993C2.91676 13.7833 6.21676 17.0833 10.0001 17.0833C13.7834 17.0833 17.0834 13.7833 17.0834 9.99993C17.0834 6.21659 13.7834 2.91659 10.0001 2.91659Z"
								stroke="currentColor" stroke-width="1.5" />
							<path d="M12.5001 10.4167L10.0001 12.9167L7.50008 10.4167" stroke="currentColor" stroke-width="1.5"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						Save Settings
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection
