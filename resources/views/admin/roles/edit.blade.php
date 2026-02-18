@extends('admin.layouts.app')

@section('title', 'Edit Role — Kiddo\'s Heaven')

@section('content')
	<form action="{{ route('admin.roles.update', $role) }}" method="POST">
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
			<!-- Basic Info -->
			<div class="col-span-12 lg:col-span-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Basic Information</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update the role details</p>
					</div>
					<div class="p-6 space-y-4">
						<div>
							<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Role Name
								*</label>
							<input type="text" id="name" name="name" value="{{ old('name', $role->name) }}"
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
							 class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('description') border-red-500 @enderror">{{ old('description', $role->description) }}</textarea>
							@error('description')
								<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" name="is_default" value="1" {{ old('is_default', $role->is_default) ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-400">Default Role</span>
							</label>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 ml-8">
								New users will be assigned this role by default
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Permissions -->
			<div class="col-span-12 lg:col-span-6">
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permissions</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select permissions for this role</p>
					</div>
					<div class="p-6 max-h-[500px] overflow-y-auto">
						@if ($permissions->isNotEmpty())
							@php
								$rolePermissionIds = $role->permissions->pluck('id')->toArray();
							@endphp
							@foreach ($permissions as $group => $groupPermissions)
								<div class="mb-6 last:mb-0">
									<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 capitalize mb-3">{{ $group }}
									</h4>
									<div class="grid grid-cols-1 gap-2">
										@foreach ($groupPermissions as $permission)
											<label
												class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-gray-800/50 cursor-pointer transition-colors">
												<input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
													class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800"
													{{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
												<div class="flex-1">
													<span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
													@if ($permission->description)
														<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $permission->description }}</p>
													@endif
												</div>
											</label>
										@endforeach
									</div>
								</div>
							@endforeach
						@else
							<div class="text-center py-8">
								<svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
								</svg>
								<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">No permissions available.</p>
								<a href="{{ route('admin.permissions.index') }}"
									class="inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
									Manage Permissions
								</a>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Actions -->
		<div class="flex items-center justify-end gap-3 mt-6">
			<a href="{{ route('admin.roles.index') }}"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
				Cancel
			</a>
			<button type="submit"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
				</svg>
				Update Role
			</button>
		</div>
	</form>
@endsection
