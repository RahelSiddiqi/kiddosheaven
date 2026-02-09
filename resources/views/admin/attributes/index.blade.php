@extends('admin.layouts.app')

@section('title', 'Product Attributes — Kiddo\'s Heaven')

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
			<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Product Attributes</h1>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage attributes for different product categories</p>
		</div>
		<div class="flex items-center gap-3">
			<a href="{{ route('admin.catalogs.index') }}"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
				</svg>
				Manage Catalog Attributes
			</a>
			<button type="button" onclick="openCreateModal()"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Add Attribute
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
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Total Attributes</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $attributes->count() }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Required Fields</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">
						{{ $attributes->where('is_required', true)->count() }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Filterable</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">
						{{ $attributes->where('is_filterable', true)->count() }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">With Values</p>
					@php
						$withValues = 0;
						foreach ($attributes as $attr) {
						    if ($attr->values && $attr->values->count() > 0) {
						        $withValues++;
						    }
						}
					@endphp
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $withValues }}</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Attributes Table -->
	<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
		<div class="overflow-x-auto" id="attributes-table-container">
			@include('admin.attributes.partials.table', ['attributes' => $attributes])
		</div>
	</div>

	<!-- Create/Edit Modal -->
	@include('admin.attributes.partials.modal')

	<!-- Delete Confirmation Modal -->
	<x-admin.common.confirm-delete :id="'delete-confirm-modal'" title="Delete Attribute"
		message="Are you sure you want to delete this attribute? This action cannot be undone." :on-confirm="'confirmDelete'"
		:on-cancel="'closeDeleteModal'" />

	@push('scripts')
		<script>
			function openCreateModal() {
				document.getElementById('attribute-form').reset();
				document.getElementById('attribute-id').value = '';
				document.getElementById('modal-title').textContent = 'Add New Attribute';
				document.getElementById('modal-submit-text').textContent = 'Save Attribute';
				document.getElementById('initial-values-container').classList.add('hidden');
				document.getElementById('attribute-modal').classList.remove('hidden');
			}

			function openEditModal(id, name, type, is_required, is_filterable, description, initialValues) {
				document.getElementById('attribute-form').reset();
				document.getElementById('attribute-id').value = id;
				document.getElementById('attr-name').value = name || '';
				document.getElementById('attr-type').value = type || 'text';
				document.getElementById('attr-required').checked = is_required == 1;
				document.getElementById('attr-filterable').checked = is_filterable == 1;
				document.getElementById('attr-description').value = description || '';
				document.getElementById('attr-initial-values').value = initialValues ? initialValues.replace(/__NEWLINE__/g,
					'\n') : '';
				document.getElementById('modal-title').textContent = 'Edit Attribute';
				document.getElementById('modal-submit-text').textContent = 'Update Attribute';

				// Show/hide initial values based on type
				const initialValuesContainer = document.getElementById('initial-values-container');
				if (type === 'select' || type === 'multiselect') {
					initialValuesContainer.classList.remove('hidden');
				} else {
					initialValuesContainer.classList.add('hidden');
				}

				document.getElementById('attribute-modal').classList.remove('hidden');
			}

			function closeModal() {
				document.getElementById('attribute-modal').classList.add('hidden');
			}

			// Show/hide initial values based on type selection
			document.getElementById('attr-type').addEventListener('change', function() {
				const initialValuesContainer = document.getElementById('initial-values-container');
				if (this.value === 'select' || this.value === 'multiselect') {
					initialValuesContainer.classList.remove('hidden');
				} else {
					initialValuesContainer.classList.add('hidden');
				}
			});

			function saveAttribute(event) {
				event.preventDefault();
				const form = event.target;
				const formData = new FormData(form);
				const id = formData.get('id');
				const url = id ? "{{ route('admin.attributes.update', ':id') }}".replace(':id', id) :
					'{{ route('admin.attributes.store') }}';
				const method = id ? 'PUT' : 'POST';

				fetch(url, {
						method: method,
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
							'Accept': 'application/json',
							'Content-Type': 'application/json',
						},
						body: JSON.stringify(Object.fromEntries(formData))
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							closeModal();
							showToast(data.message || 'Attribute saved successfully!', 'success');
							sleepReload(1500);
						} else {
							showToast(data.message || 'Error saving attribute', 'error');
						}
					})
					.catch(error => {
						console.error('Error:', error);
						showToast('Error saving attribute', 'error');
					});
			}

			function deleteAttribute(id) {
				// Open delete confirmation modal
				const modal = document.getElementById('delete-confirm-modal');
				if (modal) {
					modal.dataset.attributeId = id;
					modal.classList.remove('hidden');
				}
			}

			function confirmDelete() {
				const modal = document.getElementById('delete-confirm-modal');
				const id = modal.dataset.attributeId;
				if (id) {
					fetch("{{ route('admin.attributes.destroy', ':id') }}".replace(':id', id), {
							method: 'DELETE',
							headers: {
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
								'Accept': 'application/json',
							}
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								showToast(data.message || 'Attribute deleted successfully!', 'success');
								sleepReload(1500);
							} else {
								showToast(data.message || 'Error deleting attribute', 'error');
							}
						})
						.catch(error => {
							console.error('Error:', error);
							showToast('Error deleting attribute', 'error');
						});
				}
				modal.classList.add('hidden');
			}

			function closeDeleteModal() {
				document.getElementById('delete-confirm-modal').classList.add('hidden');
			}

			function showToast(message, type = 'success') {
				// Create toast element
				const toast = document.createElement('div');
				toast.className =
					`fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
				toast.style.animation = 'slideIn 0.3s ease-out';
				toast.innerHTML = `
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'}
                    </svg>
                    <span class="text-sm font-medium">${message}</span>
                `;
				document.body.appendChild(toast);

				// Remove after 3 seconds
				setTimeout(() => {
					toast.remove();
				}, 3000);
			}
		</script>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const tbody = document.getElementById('sortable-attributes');
				if (tbody && typeof Sortable !== 'undefined') {
					new Sortable(tbody, {
						handle: '.drag-handle',
						animation: 150,
						ghostClass: 'sortable-ghost',
						onEnd: function(evt) {
							const order = [];
							tbody.querySelectorAll('tr[data-id]').forEach(row => {
								order.push(row.dataset.id);
							});

							fetch('{{ route('admin.attributes.reorder') }}', {
									method: 'POST',
									headers: {
										'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
											.content,
										'Content-Type': 'application/json',
									},
									body: JSON.stringify({
										order: order
									})
								})
								.then(response => response.json())
								.then(data => {
									if (data.success) {
										showToast('Order saved successfully', 'success');
									}
								})
								.catch(error => {
									console.error('Error saving order:', error);
								});
						}
					});
				}
			});
		</script>
	@endpush
@endsection
