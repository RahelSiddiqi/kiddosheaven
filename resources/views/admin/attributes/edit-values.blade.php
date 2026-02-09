@extends('admin.layouts.app')

@section('title', "Edit {$attribute->name} Values — Kiddo's Heaven")

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

	<div x-data="attributeValuesManager()" class="relative">
		<!-- Header -->
		<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.attributes.index') }}"
					class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
					<svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
				<div>
					<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $attribute->name }}</h1>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage attribute values and options</p>
				</div>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.attributes.index') }}"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					All Attributes
				</a>
			</div>
		</div>

		<!-- Bulk Import Modal -->
		<div x-show="showBulkModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div @click="showBulkModal = false" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
				<div
					class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Bulk Import Values</h3>
						<button @click="showBulkModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<form @submit.prevent="submitBulkImport()">
						<div class="mb-4">
							<label for="bulk-values" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Values (one per
								line)</label>
							<textarea id="bulk-values" x-model="bulkValues" rows="6"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
							 placeholder="Red&#10;Blue&#10;Green&#10;Yellow"></textarea>
						</div>
						<div class="flex justify-end gap-2">
							<button type="button" @click="showBulkModal = false"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Cancel</button>
							<button type="submit" :disabled="bulkProcessing"
								class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 disabled:opacity50">Import</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Edit Value Modal -->
		<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div @click="showEditModal = false" class="fixed inset-0 bg-gray-900/50 bg-opacity-50 transition-opacity"></div>
				<div
					class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Value</h3>
						<button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<form @submit.prevent="submitEditValue()">
						<div class="mb-4">
							<label for="edit-value" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Value</label>
							<input type="text" id="edit-value" x-model="editValueValue" required
								class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
								placeholder="Enter value">
						</div>
						<div class="flex justify-end gap-2">
							<button type="button" @click="showEditModal = false"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Cancel</button>
							<button type="submit" :disabled="editProcessing"
								class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 disabled:opacity50">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Main Content -->
			<div class="lg:col-span-2 space-y-4">
				@if (in_array($attribute->type, ['select', 'multiselect']))
					<!-- Current Values -->
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
							<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Current Values</h2>
							<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Drag to reorder values</p>
						</div>
						<div class="p-4">
							@if ($attribute->values && $attribute->values->count() > 0)
								<div class="overflow-x-auto">
									<table class="w-full text-left border-collapse">
										<thead>
											<tr class="border-b border-gray-200 dark:border-gray-700">
												<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10">
												</th>
												<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Value
												</th>
												<th
													class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">
													Actions</th>
											</tr>
										</thead>
										<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="sortable-values">
											@foreach ($attribute->values()->orderBy('sort_order')->get() as $value)
												<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $value->id }}">
													<td class="px-4 py-3">
														<svg class="w-5 h-5 text-gray-400 drag-handle" fill="none" stroke="currentColor"
															viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
														</svg>
													</td>
													<td class="px-4 py-3 text-gray-800 dark:text-white/90 font-medium">
														{{ $value->value }}
													</td>
													<td class="px-4 py-3 text-right">
														<button type="button" @click="openEditModal({{ $value->id }}, '{{ addslashes($value->value) }}')"
															class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 mr-1">
															<x-icons.edit />
														</button>
														<button type="button" @click="openDeleteModal({{ $value->id }})"
															class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
															<x-icons.delete />
														</button>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<div class="text-center py-12">
									<svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
									</svg>
									<p class="text-gray-500 dark:text-gray-400 mb-2">No values added yet</p>
									<p class="text-sm text-gray-400 dark:text-gray-500">Use Quick Add to add values</p>
								</div>
							@endif
						</div>
					</div>
				@else
					<!-- Info for non-select types -->
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
							<h2 class="text-base font-medium text-gray-800 dark:text-white/90">About {{ ucfirst($attribute->type) }} Type
							</h2>
						</div>
						<div class="p-6">
							<div class="flex items-start gap-4">
								<div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
									<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
									</svg>
								</div>
								<div>
									<p class="text-gray-800 dark:text-white/90 font-medium mb-1">No predefined values needed</p>
									<p class="text-sm text-gray-500 dark:text-gray-400">For <strong>{{ ucfirst($attribute->type) }}</strong> type,
										values are entered directly when adding products.</p>
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>

			<!-- Sidebar -->
			<div class="lg:col-span-1 space-y-4">
				<!-- Attribute Info -->
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Attribute Details</h2>
					</div>
					<div class="p-4 space-y-3">
						<div class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
							<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $attribute->name }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Type</span>
							<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst($attribute->type) }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Required</span>
							<span
								class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $attribute->is_required ? 'Yes' : 'No' }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Filterable</span>
							<span
								class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $attribute->is_filterable ? 'Yes' : 'No' }}</span>
						</div>
						@if (in_array($attribute->type, ['select', 'multiselect']))
							<div class="flex justify-between">
								<span class="text-sm text-gray-500 dark:text-gray-400">Values</span>
								<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $attribute->values->count() }}</span>
							</div>
						@endif
					</div>
				</div>

				@if (in_array($attribute->type, ['select', 'multiselect']))
					<!-- Quick Add -->
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
							<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Quick Add</h2>
						</div>
						<div class="p-4">
							<form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST" class="flex gap-2">
								@csrf
								<input type="text" name="value" required
									class="flex-1 h-10 rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
									placeholder="Add new value...">
								<button type="submit"
									class="h-10 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
									</svg>
								</button>
								<button type="button" @click="showBulkModal = true"
									class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3"
									title="Bulk Import">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
									</svg>
								</button>
							</form>
						</div>
					</div>
				@endif

				<!-- Catalogs Using This -->
				@if ($catalogs->count() > 0)
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
							<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Used In Catalogs</h2>
						</div>
						<div class="p-4">
							<ul class="space-y-2">
								@foreach ($catalogs as $cat)
									<li class="flex items-center gap-2">
										<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
										</svg>
										<span class="text-sm text-gray-700 dark:text-gray-300">{{ $cat->name }}</span>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<div id="delete-value-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="delete-modal-title" role="dialog"
		aria-modal="true">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div class="fixed inset-0 bg-black/50 transition-opacity" onclick="window.closeDeleteModal()"></div>
			<div class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-2xl">
				<div class="p-6 text-center">
					<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
						<svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
						</svg>
					</div>
					<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Delete Value</h3>
					<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
						Are you sure you want to delete this value? This action cannot be undone.
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" onclick="window.closeDeleteModal()"
							class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button type="button" onclick="window.confirmDeleteValue()"
							class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600">
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		// Global variable to store the ID of the value being deleted
		let deletingValueId = null;
		let editingValueId = null;

		function attributeValuesManager() {
			return {
				showBulkModal: false,
				showEditModal: false,
				bulkValues: '',
				editValueValue: '',
				editProcessing: false,
				bulkProcessing: false,

				init() {
					const sortableEl = document.getElementById('sortable-values');
					if (sortableEl && typeof Sortable !== 'undefined') {
						Sortable.create(sortableEl, {
							handle: '.drag-handle',
							animation: 150,
							ghostClass: 'sortable-ghost',
							onEnd: (evt) => {
								const ids = Array.from(sortableEl.querySelectorAll('tr')).map(row => row.dataset
									.id);
								fetch('{{ route('admin.attributes.values.reorder', $attribute) }}', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': '{{ csrf_token() }}',
										'Accept': 'application/json'
									},
									body: JSON.stringify({
										order: ids
									})
								}).then(async response => {
									const text = await response.text();
									try {
										const data = JSON.parse(text);
										if (data.success) {
											this.showToast('Order updated', 'success');
										} else {
											console.error('Reorder failed:', data);
											this.showToast('Failed to update order', 'error');
										}
									} catch (e) {
										console.error('Invalid JSON response:', text);
										this.showToast('Error updating order', 'error');
									}
								});
							}
						});
					}
				},

				openDeleteModal(valueId) {
					deletingValueId = valueId;
					const modal = document.getElementById('delete-value-modal');
					if (modal) {
						modal.classList.remove('hidden');
					}
				},

				openEditModal(valueId, value) {
					editingValueId = valueId;
					this.editValueValue = value;
					this.showEditModal = true;
				},

				async submitEditValue() {
					if (!this.editValueValue.trim()) {
						this.showToast('Value cannot be empty', 'error');
						return;
					}
					this.editProcessing = true;
					try {
						const response = await fetch('/admin/attributes/{{ $attribute->id }}/values/' + editingValueId, {
							method: 'PUT',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							body: JSON.stringify({
								value: this.editValueValue
							})
						});
						const data = await response.json();
						if (data.success) {
							this.showEditModal = false;
							this.editValueValue = '';
							editingValueId = null;
							this.showToast(data.message || 'Value updated successfully', 'success');
							window.sleepReload(1000);
						} else {
							this.showToast(data.message || 'Error updating value', 'error');
						}
					} catch (error) {
						console.error('Edit error:', error);
						this.showToast('Error updating value', 'error');
					} finally {
						this.editProcessing = false;
					}
				},

				async submitBulkImport() {
					if (!this.bulkValues.trim()) {
						this.showToast('Please enter some values', 'error');
						return;
					}
					this.bulkProcessing = true;
					try {
						const response = await fetch('{{ route('admin.attributes.values.store', $attribute) }}', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							body: JSON.stringify({
								value: this.bulkValues
							})
						});
						const data = await response.json();
						if (data.success) {
							this.showBulkModal = false;
							this.bulkValues = '';
							this.showToast(data.message || 'Values imported successfully', 'success');
							window.sleepReload(1000);
						} else {
							this.showToast(data.message || 'Error importing values', 'error');
						}
					} catch (error) {
						console.error('Bulk import error:', error);
						this.showToast('Error importing values', 'error');
					} finally {
						this.bulkProcessing = false;
					}
				},

				showToast(message, type = 'success') {
					window.showToast(message, type);
				}
			}
		}

		// Global function to open delete modal
		window.openDeleteModal = function(valueId) {
			deletingValueId = valueId;
			const modal = document.getElementById('delete-value-modal');
			if (modal) {
				modal.classList.remove('hidden');
			}
		};

		// Global function to open edit modal
		window.openEditModal = function(valueId, value) {
			editingValueId = valueId;
			const modal = document.getElementById('edit-value-modal');
			if (modal) {
				document.getElementById('edit-value').value = value;
				modal.classList.remove('hidden');
			}
		};

		// Global function to close delete modal
		window.closeDeleteModal = function() {
			const modal = document.getElementById('delete-value-modal');
			if (modal) {
				modal.classList.add('hidden');
				deletingValueId = null;
			}
		};

		// Global function to confirm delete
		window.confirmDeleteValue = function() {
			if (deletingValueId) {
				fetch('/admin/attributes/{{ $attribute->id }}/values/' + deletingValueId, {
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
						'Accept': 'application/json'
					}
				}).then(response => response.json()).then(data => {
					if (data.success) {
						window.showToast('Value deleted successfully', 'success');
						window.sleepReload(1000);
					} else {
						window.showToast(data.message || 'Error deleting value', 'error');
					}
				}).catch(error => {
					console.error('Delete error:', error);
					window.showToast('Error deleting value', 'error');
				});
			}
			window.closeDeleteModal();
		};

		window.showToast = function(message, type = 'success') {
			const toast = document.createElement('div');
			toast.className =
				`fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
			toast.innerHTML =
				`<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'}</svg><span class="text-sm font-medium">${message}</span>`;
			document.body.appendChild(toast);
			setTimeout(() => toast.remove(), 3000);
		};

		window.sleepReload = function(ms) {
			setTimeout(() => {
				window.location.reload();
			}, ms);
		};
	</script>
@endpush
