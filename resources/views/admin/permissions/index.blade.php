@extends('admin.layouts.app')

@section('title', 'Permissions — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="permissionManager({
    baseUrl: '/admin/permissions',
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
				<x-admin.ui.delete-modal
					modalId="deletePermissionModal"
					title="Delete Permission"
					message="Are you sure you want to delete this permission? This action cannot be undone."
				/>

				<!-- Header -->
				<div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Permissions</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage system permissions.</p>
					</div>
					<a href="{{ route('admin.permissions.create') }}"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
						Create Permission
					</a>
				</div>

				@forelse($permissions as $group => $groupPermissions)
					<div class="mb-6">
						<h4 class="text-md font-semibold text-gray-800 dark:text-white/90 mb-3 capitalize">{{ $group }}</h4>
						<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
							<div class="grid gap-4 p-5 md:grid-cols-2 lg:grid-cols-3">
								@foreach ($groupPermissions as $permission)
									<div
										class="flex items-center justify-between p-4 rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
										<div>
											<p class="font-medium text-sm text-gray-900 dark:text-white">{{ $permission->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->slug }}</p>
										</div>
										<div class="flex items-center gap-2">
											<a href="{{ route('admin.permissions.edit', $permission) }}"
												class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg" title="Edit">
												<x-icons.edit />
											</a>
											<button
												@click="$dispatch('open-delete-modal', { url: '/admin/permissions/{{ $permission->id }}', id: {{ $permission->id }}, name: '{{ addslashes($permission->name) }}' })"
												class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg"
												title="Delete">
												<x-icons.delete />
											</button>
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				@empty
					<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
						<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
								d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
						</svg>
						<p class="text-sm font-medium text-gray-900 dark:text-white">No permissions yet</p>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Get started by creating your first permission.</p>
					</div>
				@endforelse
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('permissionManager', (config) => ({
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
