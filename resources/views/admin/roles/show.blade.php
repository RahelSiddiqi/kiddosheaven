@extends('admin.layouts.app')

@section('title', 'Role Details — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Basic Info -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $role->name }}</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Role details and information</p>
					</div>
					<div class="flex items-center gap-2">
						<a href="{{ route('admin.roles.edit', $role) }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
							</svg>
							Edit Role
						</a>
					</div>
				</div>
				<div class="p-6">
					<dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
						<div>
							<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role Name</dt>
							<dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->name }}</dd>
						</div>
						<div>
							<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
							<dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->slug }}</dd>
						</div>
						@if ($role->description)
							<div class="sm:col-span-2">
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
								<dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->description }}</dd>
							</div>
						@endif
						<div>
							<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
							<dd class="mt-1">
								@if ($role->is_default)
									<span
										class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Default
										Role</span>
								@else
									<span
										class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Custom
										Role</span>
								@endif
							</dd>
						</div>
						<div>
							<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</dt>
							<dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->users->count() }} users</dd>
						</div>
					</dl>
				</div>
			</div>

			<!-- Assigned Users -->
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 mt-6">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Assigned Users</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Users with this role</p>
				</div>
				<div class="p-6">
					@if ($role->users->isNotEmpty())
						<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
							<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-800/50">
									<tr>
										<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
											Name</th>
										<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
											Email</th>
										<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
											Joined</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200 dark:bg-transparent dark:divide-gray-700">
									@foreach ($role->users as $user)
										<tr>
											<td class="px-4 py-3 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
											</td>
											<td class="px-4 py-3 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
											</td>
											<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
												{{ $user->created_at->format('M d, Y') }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-8">
							<svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
							</svg>
							<p class="text-sm text-gray-500 dark:text-gray-400">No users assigned to this role yet.</p>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Permissions -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permissions</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $role->permissions->count() }} permissions
						assigned</p>
				</div>
				<div class="p-6 max-h-[600px] overflow-y-auto">
					@if ($role->permissions->isNotEmpty())
						@php
							$groupedPermissions = $role->permissions->groupBy('group');
						@endphp
						@foreach ($groupedPermissions as $group => $permissions)
							<div class="mb-6 last:mb-0">
								<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 capitalize mb-3">{{ $group }}</h4>
								<div class="space-y-2">
									@foreach ($permissions as $permission)
										<div class="flex items-start gap-2 p-2.5 rounded-lg bg-gray-50 dark:bg-gray-800/50">
											<svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
											</svg>
											<div>
												<span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
												@if ($permission->description)
													<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $permission->description }}</p>
												@endif
											</div>
										</div>
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
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">No permissions assigned to this role.</p>
							<a href="{{ route('admin.roles.edit', $role) }}"
								class="inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
								Add Permissions
							</a>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Back Button -->
	<div class="mt-6">
		<a href="{{ route('admin.roles.index') }}"
			class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
			<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
			</svg>
			Back to Roles
		</a>
	</div>
@endsection
