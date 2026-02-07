@extends('admin.layouts.app')

@section('title', 'Roles — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="roleManager({
    baseUrl: '/admin/roles',
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

				<!-- Delete Confirmation Modal -->
				<x-admin.ui.delete-modal modalId="deleteRoleModal" title="Delete Role"
					message="Are you sure you want to delete this role? This action cannot be undone." />

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Roles</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage user roles and permissions.</p>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<a href="{{ route('admin.roles.create') }}"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
								Create Role
							</a>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Role
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Slug
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Users</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Permissions</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@forelse ($roles as $role)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<a href="{{ route('admin.roles.show', $role) }}"
													class="font-medium text-sm text-gray-900 hover:underline dark:text-white">
													{{ $role->name }}
												</a>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $role->slug }}</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $role->users->count() }} users</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $role->permissions->count() }} permissions</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($role->is_default)
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Default</span>
												@else
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Custom</span>
												@endif
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<a href="{{ route('admin.roles.edit', $role) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.edit />
													</a>
													@if (!$role->is_default && $role->users->count() === 0)
														<button
															@click="$dispatch('open-delete-modal', { url: '/admin/roles/{{ $role->id }}', id: {{ $role->id }}, name: '{{ addslashes($role->name) }}' })"
															class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
															<x-icons.delete />
														</button>
													@endif
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No roles found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
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
			Alpine.data('roleManager', (config) => ({
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
			}))
		})
	</script>
@endpush
