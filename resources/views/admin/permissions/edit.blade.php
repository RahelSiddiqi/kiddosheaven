@extends('admin.layouts.app')

@section('title', 'Edit Permission — Kiddo\'s Heaven')

@section('content')
	<form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
		@csrf
		@method('PUT')

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
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update the permission details</p>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Permission Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name', $permission->name) }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('name') border-red-500 @enderror"
								required>
							@error('name')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Slug
								(Read-only)</label>
							<input type="text" id="slug" value="{{ $permission->slug }}" readonly
								class="h-11 w-full rounded-lg border border-gray-300 bg-gray-100 py-2.5 px-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 cursor-not-allowed">
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
								Slug is automatically generated from the name
							</p>
						</div>

						<div>
							<label for="description"
								class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
							<textarea id="description" name="description" rows="3"
							 class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('description') border-red-500 @enderror">{{ old('description', $permission->description) }}</textarea>
							@error('description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="group" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Group</label>
							<input type="text" id="group" name="group" value="{{ old('group', $permission->group) }}"
								placeholder="e.g., users, products, orders"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('group') border-red-500 @enderror">
							@error('group')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
								Group helps organize permissions
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Help Card -->
			<div class="col-span-12 lg:col-span-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permission Details</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current permission information</p>
					</div>
					<div class="p-6 space-y-4">
						<div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</p>
								<p class="text-sm font-semibold text-gray-800 dark:text-white/90">
									{{ $permission->created_at->format('M d, Y H:i') }}</p>
							</div>
							<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>

						<div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
							<div>
								<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</p>
								<p class="text-sm font-semibold text-gray-800 dark:text-white/90">
									{{ $permission->updated_at->format('M d, Y H:i') }}</p>
							</div>
							<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
						</div>

						<div class="flex items-center justify-between p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
							<div>
								<p class="text-sm font-medium text-blue-600 dark:text-blue-400">Assigned to Roles</p>
								<p class="text-sm font-semibold text-blue-800 dark:text-blue-300">
									{{ $permission->roles->count() }} role(s)</p>
							</div>
							<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
							</svg>
						</div>

						@if ($permission->roles->isNotEmpty())
							<div class="pt-2">
								<p class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Roles with this permission:</p>
								<div class="flex flex-wrap gap-2">
									@foreach ($permission->roles as $role)
										<span
											class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
											{{ $role->name }}
										</span>
									@endforeach
								</div>
							</div>
						@endif
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
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
				</svg>
				Update Permission
			</button>
		</div>
	</form>
@endsection
