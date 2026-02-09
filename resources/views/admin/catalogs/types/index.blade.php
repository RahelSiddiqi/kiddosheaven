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
			opacity: 0.4;
			background: #f3f4f6;
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
			<button onclick="openCreateModal()"
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
									<a href="{{ route('admin.catalogs.types.attributes', $type) }}"
										class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
										<x-icons.edit />
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
	<div x-data="catalogTypeManager()" x-cloak>
		<!-- Create Modal -->
		<div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div @click="showCreateModal = false" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
				<div
					class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Catalog Type</h3>
						<button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<form @submit.prevent="createType()">
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
							<input type="text" x-model="createForm.name" required
								class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								placeholder="e.g., Puzzles & Brain Teasers">
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
							<textarea x-model="createForm.description" rows="3"
							 class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							 placeholder="Optional description"></textarea>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon (CSS class or emoji)</label>
							<input type="text" x-model="createForm.icon"
								class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								placeholder="e.g., 🧩 or 'fas fa-puzzle-piece'">
						</div>
						<div class="mb-4">
							<label class="flex items-center gap-2 cursor-pointer">
								<input type="checkbox" x-model="createForm.is_active" class="w-4 h-4 rounded border-gray-300 text-blue-500">
								<span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
							</label>
						</div>
						<div class="flex justify-end gap-2">
							<button type="button" @click="showCreateModal = false"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</button>
							<button type="submit" :disabled="createForm.processing"
								class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 disabled:opacity-50">
								Create Type
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Edit Modal -->
		<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div @click="showEditModal = false" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
				<div
					class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Catalog Type</h3>
						<button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<form @submit.prevent="updateType()">
						<input type="hidden" x-model="editForm.id">
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
							<input type="text" x-model="editForm.name" required
								class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
							<textarea x-model="editForm.description" rows="3"
							 class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"></textarea>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
							<input type="text" x-model="editForm.icon"
								class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div class="mb-4">
							<label class="flex items-center gap-2 cursor-pointer">
								<input type="checkbox" x-model="editForm.is_active" class="w-4 h-4 rounded border-gray-300 text-blue-500">
								<span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
							</label>
						</div>
						<div class="flex justify-end gap-2">
							<button type="button" @click="showEditModal = false"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</button>
							<button type="submit" :disabled="editForm.processing"
								class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 disabled:opacity-50">
								Update Type
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Delete Confirmation Modal -->
		<div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div @click="showDeleteModal = false" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
				<div
					class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
					<div class="flex flex-col items-center">
						<div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
							<svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
						</div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">Delete Catalog Type?</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
							Are you sure you want to delete "<span x-text="deleteForm.name" class="font-medium"></span>"?
							@php
								// Note: This is a simplified check - in real app, you'd pass the count
							@endphp
							<br><span class="text-red-500">This action cannot be undone.</span>
						</p>
						<div class="flex gap-3">
							<button type="button" @click="showDeleteModal = false"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</button>
							<button type="button" @click="confirmDelete()" :disabled="deleteForm.processing"
								class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 disabled:opacity-50">
								Delete
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- SortableJS CDN -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

	<script>
		function catalogTypeManager() {
			return {
				types: @json($types),
				showCreateModal: false,
				showEditModal: false,
				showDeleteModal: false,
				createForm: {
					name: '',
					description: '',
					icon: '',
					is_active: true,
					processing: false
				},
				editForm: {
					id: null,
					name: '',
					description: '',
					icon: '',
					is_active: true,
					processing: false
				},
				deleteForm: {
					id: null,
					name: '',
					processing: false
				},

				init() {
					// Initialize Sortable
					Sortable.create(document.getElementById('sortable-types'), {
						handle: '.drag-handle',
						animation: 150,
						ghostClass: 'sortable-ghost',
						onEnd: (evt) => {
							// Update order via AJAX
							const item = evt.item;
							const newIndex = evt.newIndex;
							const oldIndex = evt.oldIndex;

							// Get all IDs in new order
							const ids = Array.from(document.querySelectorAll('#sortable-types tr'))
								.map(row => row.dataset.id);

							// Send to server
							fetch('/admin/catalog.types/reorder', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': '{{ csrf_token() }}'
									},
									body: JSON.stringify({
										ids
									})
								})
								.then(response => response.json())
								.then(data => {
									if (data.success) {
										// Show success toast
										this.showToast('Order updated successfully', 'success');
									}
								})
								.catch(error => {
									console.error('Error reordering:', error);
									// Revert visual order
									item.parentNode.insertBefore(item, item.parentNode.children[oldIndex]);
								});
						}
					});
				},

				openCreateModal() {
					this.createForm = {
						name: '',
						description: '',
						icon: '',
						is_active: true,
						processing: false
					};
					this.showCreateModal = true;
				},

				async createType() {
					this.createForm.processing = true;

					try {
						const response = await fetch('/admin/catalog.types', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							body: JSON.stringify(this.createForm)
						});

						const data = await response.json();

						if (data.success) {
							this.showCreateModal = false;
							this.showToast(data.message, 'success');
							window.location.reload();
						} else {
							this.showToast(data.message || 'Error creating type', 'error');
						}
					} catch (error) {
						this.showToast('Error creating type', 'error');
					} finally {
						this.createForm.processing = false;
					}
				},

				openEditModal(id, name, description, icon, is_active) {
					this.editForm = {
						id,
						name,
						description,
						icon,
						is_active,
						processing: false
					};
					this.showEditModal = true;
				},

				async updateType() {
					this.editForm.processing = true;

					try {
						const response = await fetch(`/admin/catalog.types/${this.editForm.id}`, {
							method: 'PUT',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							body: JSON.stringify(this.editForm)
						});

						const data = await response.json();

						if (data.success) {
							this.showEditModal = false;
							this.showToast(data.message, 'success');
							window.location.reload();
						} else {
							this.showToast(data.message || 'Error updating type', 'error');
						}
					} catch (error) {
						this.showToast('Error updating type', 'error');
					} finally {
						this.editForm.processing = false;
					}
				},

				openDeleteConfirm(id, name) {
					this.deleteForm = {
						id,
						name,
						processing: false
					};
					this.showDeleteModal = true;
				},

				async confirmDelete() {
					this.deleteForm.processing = true;

					try {
						const response = await fetch(`/admin/catalog.types/${this.deleteForm.id}`, {
							method: 'DELETE',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							}
						});

						const data = await response.json();

						if (data.success) {
							this.showDeleteModal = false;
							this.showToast(data.message, 'success');
							window.location.reload();
						} else {
							this.showToast(data.message || 'Error deleting type', 'error');
						}
					} catch (error) {
						this.showToast('Error deleting type', 'error');
					} finally {
						this.deleteForm.processing = false;
					}
				},

				showToast(message, type = 'success') {
					// Create toast element
					const toast = document.createElement('div');
					toast.className = `fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70 ${
						type === 'success' ? 'bg-green-500' : 'bg-red-500'
					}`;
					toast.innerHTML = `
						<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							${type === 'success'
								? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
								: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
							}
						</svg>
						<span class="text-sm font-medium">${message}</span>
					`;
					document.body.appendChild(toast);

					// Remove after 3 seconds
					setTimeout(() => {
						toast.remove();
					}, 3000);
				}
			}
		}
	</script>
@endsection
