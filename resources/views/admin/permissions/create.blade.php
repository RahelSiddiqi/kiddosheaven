@extends('admin.layouts.app')

@section('title', 'Create Permission — Kiddo\'s Heaven')

@section('content')
	<form action="{{ route('admin.permissions.store') }}" method="POST">
		@csrf

		<!-- Toast Notification -->
		@if (session('success'))
			<div x-data="{ show: true }" x-show="show" x-transition
				class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-70"
				style="display: none;">
				<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
				</svg>
				<span class="text-sm font-medium">{{ session('success') }}</span>
			</div>
		@endif

		<div class="grid grid-cols-12 gap-4 md:gap-6">
			<!-- Permission Info -->
			<div class="col-span-12 lg:col-span-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permission Information</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter the permission details</p>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Permission Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name') }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('name') border-red-500 @enderror"
								required>
							@error('name')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
							<textarea id="description" name="description" rows="3"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
							@error('description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="group" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Group</label>
							<input type="text" id="group" name="group" value="{{ old('group', 'general') }}"
								placeholder="e.g., users, products, orders"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('group') border-red-500 @enderror">
							@error('group')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
								Group helps organize permissions (default: general)
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Help Card -->
			<div class="col-span-12 lg:col-span-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permission Guidelines</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Best practices for creating permissions</p>
					</div>
					<div class="p-6 space-y-4">
						<div class="flex items-start gap-3">
							<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
								<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<div>
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Naming Convention</h4>
								<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
									Use descriptive names like "Create Users", "Edit Products", "View Orders"
								</p>
							</div>
						</div>

						<div class="flex items-start gap-3">
							<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
								<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<div>
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Grouping</h4>
								<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
									Group related permissions (e.g., "users", "products", "orders", "settings")
								</p>
							</div>
						</div>

						<div class="flex items-start gap-3">
							<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
								<svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							</div>
							<div>
								<h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Security</h4>
								<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
									Only create permissions that are necessary for your roles and users
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Actions -->
		<div class="flex items-center justify-end gap-3 mt-6">
			<a href="{{ route('admin.permissions.index') }}"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
				Cancel
			</a>
			<button type="submit"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Create Permission
			</button>
		</div>
	</form>
@endsection
