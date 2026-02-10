@extends('admin.layouts.app')

@section('title', 'Catalog Types — Kiddo\'s Heaven')

@push('styles')
	<style>
		.drag-handle {
			cursor: grab;
		}

		.drag-handle:active {
			cursor: grabbing;
		}

		.sortable-ghost {
			background-color: rgb(239 246 255) !important;
		}

		.dark .sortable-ghost {
			background-color: rgb(31 41 55) !important;
		}
	</style>
@endpush

@section('content')
	<!-- Toast Notification -->
	@if (session('success'))
		<div x-data="{ show: true }" x-show="show" x-transition
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-70"
			style="animation: slideIn 0.3s ease-out;">
			<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<span class="text-sm font-medium">{{ session('success') }}</span>
		</div>
	@endif

	<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
		<div>
			<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Catalog Types</h1>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage product categories and their types</p>
		</div>
		<div class="flex items-center gap-3">
			<button type="button" onclick="openCreateModal()"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Add Type
			</button>
		</div>
	</div>

	<!-- Quick Stats -->
	<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Total Types</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ count($types) }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Active Types</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $types->where('is_active', true)->count() }}
					</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
					<svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">With Catalogs</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">
						{{ $types->loadCount('catalogs')->where('catalogs_count', '>', 0)->count() }}
					</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Inactive Types</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $types->where('is_active', false)->count() }}
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Types Table -->
	<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
		<div class="overflow-x-auto" id="types-table-container">
			<table class="w-full text-left border-collapse">
				<thead>
					<tr class="border-b border-gray-200 dark:border-gray-700">
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10"></th>
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type Name
						</th>
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Slug</th>
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Catalogs</th>
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
						<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">
							Actions</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="sortable-types">
					@forelse ($types as $type)
						<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $type->id }}">
							<td class="px-6 py-4">
								<svg class="w-5 h-5 text-gray-400 drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
								</svg>
							</td>
							<td class="px-6 py-4">
								<div class="flex flex-col">
									<span class="font-medium text-gray-800 dark:text-white/90">{{ $type->name }}</span>
									@if ($type->description)
										<span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($type->description, 50) }}</span>
									@endif
								</div>
							</td>
							<td class="px-6 py-4">
								<code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $type->slug }}</code>
							</td>
							<td class="px-6 py-4">
								<span class="text-sm text-gray-600 dark:text-gray-400">{{ $type->catalogs_count ?? 0 }}</span>
							</td>
							<td class="px-6 py-4">
								@if ($type->is_active)
									<span
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
										Active
									</span>
								@else
									<span
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
										Inactive
									</span>
								@endif
							</td>
							<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
								<div class="flex items-center gap-2 justify-end">
									<a href="{{ route('admin.catalogs.types.attributes.index', $type) }}"
										class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
										<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
										</svg>
									</a>
									<button type="button"
										onclick="openEditModal({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ addslashes($type->description ?? '') }}', '{{ addslashes($type->icon ?? '') }}', {{ $type->is_active ? 1 : 0 }})"
										class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
										<x-icons.edit />
									</button>
									<button type="button" onclick="deleteType({{ $type->id }}, '{{ addslashes($type->name) }}')"
										class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-red-600">
										<x-icons.delete />
									</button>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="px-6 py-12 text-center">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
									</svg>
									<p class="text-gray-500 dark:text-gray-400 mb-2">No catalog types found</p>
									<p class="text-sm text-gray-400 dark:text-gray-500">Create your first catalog type to organize your products
									</p>
								</div>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<!-- Create Modal -->
	<div id="create-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div onclick="closeModal('create-modal')" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity">
			</div>
			<div
				class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
				<div class="flex items-center justify-between mb-4">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Catalog Type</h3>
					<button onclick="closeModal('create-modal')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form id="create-form" onsubmit="handleCreate(event)">
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
						<input type="text" id="create-name" required
							class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							placeholder="e.g., Puzzles & Brain Teasers">
					</div>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
						<textarea id="create-description" rows="3"
						 class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
						 placeholder="Optional description"></textarea>
					</div>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon (CSS class or emoji)</label>
						<input type="text" id="create-icon"
							class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							placeholder="e.g., or 'fas fa-puzzle-piece'">
					</div>
					<div class="mb-4">
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" id="create-is_active" class="w-4 h-4 rounded border-gray-300 text-blue-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
						</label>
					</div>
					<div class="flex justify-end gap-2">
						<button type="button" onclick="closeModal('create-modal')"
							class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button type="submit" id="create-submit"
							class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
							Create Type
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Edit Modal -->
	<div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div onclick="closeModal('edit-modal')" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
			<div
				class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
				<div class="flex items-center justify-between mb-4">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Catalog Type</h3>
					<button onclick="closeModal('edit-modal')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form id="edit-form" onsubmit="handleUpdate(event)">
					<input type="hidden" id="edit-id">
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
						<input type="text" id="edit-name" required
							class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
						<textarea id="edit-description" rows="3"
						 class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"></textarea>
					</div>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
						<input type="text" id="edit-icon"
							class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="mb-4">
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" id="edit-is_active" class="w-4 h-4 rounded border-gray-300 text-blue-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
						</label>
					</div>
					<div class="flex justify-end gap-2">
						<button type="button" onclick="closeModal('edit-modal')"
							class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button type="submit" id="edit-submit"
							class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
							Update Type
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<x-admin.common.confirm-delete :id="'delete-confirm-modal'" title="Delete Catalog Type"
		message="Are you sure you want to delete <span id='delete-name' class='font-medium text-red-600'></span>? This action cannot be undone."
		:on-confirm="'confirmDelete'" :on-cancel="'closeDeleteModal'" />
@endsection

@push('scripts')
	<script>
		let currentDeleteId = null;

		function openCreateModal() {
			document.getElementById('create-form').reset();
			document.getElementById('create-modal').classList.remove('hidden');
		}

		function openEditModal(id, name, description, icon, is_active) {
			document.getElementById('edit-id').value = id;
			document.getElementById('edit-name').value = name || '';
			document.getElementById('edit-description').value = description || '';
			document.getElementById('edit-icon').value = icon || '';
			document.getElementById('edit-is_active').checked = is_active == 1;
			document.getElementById('edit-modal').classList.remove('hidden');
		}

		function deleteType(id, name) {
			currentDeleteId = id;
			const modal = document.getElementById('delete-confirm-modal');
			const nameSpan = modal.querySelector('#delete-name');
			if (nameSpan) {
				nameSpan.textContent = name;
			}
			modal.classList.remove('hidden');
		}

		function closeModal(modalId) {
			document.getElementById(modalId).classList.add('hidden');
		}

		function closeDeleteModal() {
			document.getElementById('delete-confirm-modal').classList.add('hidden');
			currentDeleteId = null;
		}

		async function handleCreate(event) {
			event.preventDefault();
			const submitBtn = document.getElementById('create-submit');
			submitBtn.disabled = true;
			submitBtn.textContent = 'Creating...';

			const data = {
				name: document.getElementById('create-name').value,
				description: document.getElementById('create-description').value,
				icon: document.getElementById('create-icon').value,
				is_active: document.getElementById('create-is_active').checked
			};
			console.log('Sending create data:', data);

			try {
				const response = await fetch('{{ route('admin.catalogs.types.store') }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
						'Accept': 'application/json'
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();
				console.log('Server response:', result);

				if (response.ok && result.success) {
					closeModal('create-modal');
					showToast(result.message || 'Catalog type created successfully', 'success');
					setTimeout(() => window.location.reload(), 1500);
				} else {
					showToast(result.message || result.error || 'Error creating type', 'error');
				}
			} catch (error) {
				console.error('Create error:', error);
				showToast('Error creating type: ' + error.message, 'error');
			} finally {
				submitBtn.disabled = false;
				submitBtn.textContent = 'Create Type';
			}
		}

		async function handleUpdate(event) {
			event.preventDefault();
			const id = document.getElementById('edit-id').value;
			const submitBtn = document.getElementById('edit-submit');
			submitBtn.disabled = true;
			submitBtn.textContent = 'Updating...';

			const data = {
				name: document.getElementById('edit-name').value,
				description: document.getElementById('edit-description').value,
				icon: document.getElementById('edit-icon').value,
				is_active: document.getElementById('edit-is_active').checked
			};

			try {
				const response = await fetch(`{{ route('admin.catalogs.types.update', ['type' => '__ID__']) }}`.replace(
					'__ID__', id), {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
						'Accept': 'application/json',
						'X-HTTP-Method-Override': 'PUT'
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();
				console.log('Server response:', result);

				if (response.ok && result.success) {
					closeModal('edit-modal');
					showToast(result.message || 'Catalog type updated successfully', 'success');
					setTimeout(() => window.location.reload(), 1500);
				} else {
					showToast(result.message || result.error || 'Error updating type', 'error');
				}
			} catch (error) {
				console.error('Update error:', error);
				showToast('Error updating type: ' + error.message, 'error');
			} finally {
				submitBtn.disabled = false;
				submitBtn.textContent = 'Update Type';
			}
		}

		async function confirmDelete() {
			if (!currentDeleteId) {
				showToast('No item selected for deletion', 'error');
				return;
			}

			const submitBtn = document.querySelector('#delete-confirm-modal .bg-red-500');
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Deleting...';
			}

			try {
				const response = await fetch(`{{ route('admin.catalogs.types.destroy', ['type' => '__ID__']) }}`.replace(
					'__ID__', currentDeleteId), {
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
						'Accept': 'application/json'
					}
				});

				const result = await response.json();
				console.log('Server response:', result);

				if (response.ok && result.success) {
					closeDeleteModal();
					showToast(result.message || 'Catalog type deleted successfully', 'success');
					setTimeout(() => window.location.reload(), 1500);
				} else {
					showToast(result.message || result.error || 'Error deleting type', 'error');
				}
			} catch (error) {
				console.error('Delete error:', error);
				showToast('Error deleting type: ' + error.message, 'error');
			} finally {
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = 'Delete';
				}
			}
		}

		function showToast(message, type = 'success') {
			const toast = document.createElement('div');
			toast.className =
				`fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
			toast.innerHTML = `
				<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}" />
				</svg>
				<span class="text-sm font-medium">${message}</span>
			`;
			document.body.appendChild(toast);

			setTimeout(() => {
				toast.style.opacity = '0';
				toast.style.transition = 'opacity 0.3s ease';
				setTimeout(() => toast.remove(), 300);
			}, 3000);
		}

		document.addEventListener('DOMContentLoaded', function() {
			// Initialize SortableJS
			const tableBody = document.getElementById('sortable-types');
			if (tableBody && typeof Sortable !== 'undefined') {
				new Sortable(tableBody, {
					animation: 150,
					handle: '.drag-handle',
					ghostClass: 'sortable-ghost',
					dragClass: 'opacity-50',
					onEnd: async function(evt) {
						const rows = tableBody.querySelectorAll('tr');
						const order = [];
						rows.forEach(row => {
							order.push(row.dataset.id);
						});

						console.log('Saving new order:', order);

						try {
							const response = await fetch('{{ route('admin.catalogs.types.reorder') }}', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'X-CSRF-TOKEN': document.querySelector(
										'meta[name="csrf-token"]').content
								},
								body: JSON.stringify({
									order: order
								})
							});

							const result = await response.json();
							if (result.success) {
								showToast(result.message || 'Order saved successfully', 'success');
							} else {
								showToast(result.message || 'Error saving order', 'error');
							}
						} catch (error) {
							console.error('Sortable error:', error);
							showToast('Error saving order', 'error');
						}
					}
				});
			} else if (typeof Sortable === 'undefined') {
				console.warn('SortableJS not loaded');
			}
		});
	</script>
@endpush
