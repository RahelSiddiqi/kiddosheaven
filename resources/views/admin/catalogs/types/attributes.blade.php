@extends('admin.layouts.app')

@section('title', 'Manage Attributes for ' . $type->name . ' — Kiddo\'s Heaven')

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
			<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">
				Manage Attributes: {{ $type->name }}
			</h1>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
				Assign attributes to this catalog type
			</p>
		</div>
		<div class="flex items-center gap-3">
			<a href="{{ route('admin.catalogs.types.index') }}"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
				</svg>
				Back to Types
			</a>
			<button type="button" onclick="openAddModal()"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Add Attribute
			</button>
		</div>
	</div>

	<!-- Quick Stats -->
	<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Assigned Attributes</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $typeAttributes->count() }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Available Attributes</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">
						{{ $allAttributes->count() - $typeAttributes->count() }}</p>
				</div>
			</div>
		</div>
		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-4">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
					<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M19 11H5m14 0a2 2 0 012 2v2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
					</svg>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Total Attributes</p>
					<p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $allAttributes->count() }}</p>
				</div>
			</div>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6" x-data="attributeManager()">
		<!-- Assigned Attributes -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Assigned Attributes</h3>
					<div class="flex items-center gap-2">
						@if ($typeAttributes->count() > 1)
							<button type="button"
								class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-500/15 dark:text-blue-400">
								<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
								</svg>
								Drag to reorder
							</button>
						@endif
					</div>
				</div>
				<div class="p-5">
					@if ($typeAttributes->count() > 0)
						<div class="overflow-x-auto">
							<table class="w-full whitespace-nowrap" id="attributes-table">
								<thead>
									<tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
										<th class="px-4 py-3 w-10"></th>
										<th class="px-4 py-3">Attribute</th>
										<th class="px-4 py-3">Type</th>
										<th class="px-4 py-3">Required</th>
										<th class="px-4 py-3 text-right">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="sortable-attributes">
									@foreach ($typeAttributes as $attribute)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $attribute->id }}">
											<td class="px-4 py-3">
												<svg class="w-5 h-5 text-gray-400 drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
												</svg>
											</td>
											<td class="px-4 py-3">
												<span class="font-medium text-gray-800 dark:text-white/90">{{ $attribute->name }}</span>
											</td>
											<td class="px-4 py-3">
												<span
													class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
													{{ ucfirst($attribute->type) }}
												</span>
											</td>
											<td class="px-4 py-3">
												@if ($attribute->pivot->is_required)
													<span
														class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
														Required
													</span>
												@else
													<span class="text-sm text-gray-500 dark:text-gray-400">Optional</span>
												@endif
											</td>
											<td class="px-4 py-3 text-right">
												<div class="flex items-center gap-2 justify-end">
													<button @click="openDeleteModal({{ $attribute->id }}, '{{ addslashes($attribute->name) }}')"
														class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-600" title="Detach">
														<x-icons.delete />
													</button>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-12">
							<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
								</svg>
							</div>
							<h4 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-1">No Attributes Assigned</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add attributes from the list below</p>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Available Attributes -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex flex-col gap-4 p-5 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Available Attributes</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400">Click to add attributes</p>
				</div>
				<div class="p-5">
					@if ($allAttributes->count() > 0)
						<div class="space-y-2">
							@foreach ($allAttributes as $attribute)
								@if (!$typeAttributes->contains('id', $attribute->id))
									<div
										class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
										<div>
											<p class="font-medium text-gray-800 dark:text-white/90">{{ $attribute->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($attribute->type) }}</p>
										</div>
										<button type="button"
											onclick="attachAttribute({{ $attribute->id }}, '{{ addslashes($attribute->name) }}')"
											class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Attach">
											<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
											</svg>
										</button>
									</div>
								@endif
							@endforeach
						</div>
						@if ($typeAttributes->count() == $allAttributes->count())
							<div class="text-center py-8">
								<p class="text-sm text-gray-500 dark:text-gray-400">All attributes assigned</p>
							</div>
						@endif
					@else
						<div class="text-center py-8">
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No attributes available</p>
							<a href="{{ route('admin.attributes.index') }}"
								class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Create
								Attribute</a>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Add Attribute Modal -->
	<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 bg-black/50 transition-opacity">
			</div>
			<div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-10" x-transition>
				<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Attribute</h3>
					<button @click="showAddModal = false" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form @submit.prevent="submitAddForm()">
					@csrf
					<div class="p-5">
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Attribute</label>
							<select x-model="selectedAttribute" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 dark:bg-gray-900 dark:text-white">
								<option value="">Choose an attribute...</option>
								@foreach ($allAttributes as $attribute)
									@if (!$typeAttributes->contains('id', $attribute->id))
										<option value="{{ $attribute->id }}">{{ $attribute->name }} ({{ ucfirst($attribute->type) }})</option>
									@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
						<button type="button" @click="showAddModal = false"
							class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancel</button>
						<button type="submit"
							class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Add Attribute</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div x-show="showDeleteModal" @click="showDeleteModal = false"
				class="fixed inset-0 bg-black/50 transition-opacity"></div>
			<div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-10" x-transition>
				<div class="flex items-center justify-center p-5">
					<div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
						<svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
						</svg>
					</div>
				</div>
				<div class="text-center px-5">
					<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">Detach Attribute</h3>
					<p class="mb-5 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to detach <strong
							x-text="deleteAttributeName" class="text-gray-700 dark:text-gray-200"></strong>?</p>
				</div>
				<div class="flex items-center justify-center gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
					<button @click="showDeleteModal = false"
						class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancel</button>
					<button @click="confirmDetach()" :disabled="isDetaching"
						class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 disabled:opacity-50">
						<span x-show="!isDetaching">Detach</span>
						<span x-show="isDetaching" class="flex items-center justify-center gap-2">
							<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
								</circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
							</svg>
							Detaching...
						</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Toast -->
	<div x-show="toastShow" x-transition
		class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
		:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'" style="display: none;">
		<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
		</svg>
		<span x-text="toastMessage" class="text-sm font-medium"></span>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const sortableEl = document.getElementById('sortable-attributes');
			const reorderRoute = '{{ route('admin.catalogs.types.test-reorder', $type) }}';
			const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

			if (sortableEl && typeof Sortable !== 'undefined') {
				Sortable.create(sortableEl, {
					animation: 150,
					handle: '.drag-handle',
					ghostClass: 'sortable-ghost',
					dragClass: 'opacity-50',
					onEnd: async function(evt) {
						const rows = sortableEl.querySelectorAll('tr');
						const order = [];
						rows.forEach((row, index) => {
							order.push(parseInt(row.dataset.id, 10));
						});

						try {
							const response = await fetch(reorderRoute, {
								const formData = new FormData();
								formData.append('order', JSON.stringify(order));
								formData.append('_token', csrfToken);

								const response = await fetch(reorderRoute, {
									method: 'POST',
									credentials: 'same-origin',
									body: formData
								});
							});

							const result = await response.json();

							if (result.success) {
								alert('Order saved successfully! New order: ' + order.join(', '));
								setTimeout(() => window.location.reload(), 1000);
							} else {
								alert(result.message || 'Error saving order');
							}
						} catch (error) {
							console.error('Fetch error:', error);
							try {
								const formData = new FormData();
								formData.append('order', JSON.stringify(order));
								formData.append('_token', csrfToken);

								const fallbackResponse = await fetch(reorderRoute, {
									method: 'POST',
									credentials: 'same-origin',
									body: formData
								});

								const fallbackResult = await fallbackResponse.json();
								console.log('Fallback response:', fallbackResult);

								if (fallbackResult.success) {
									alert('Order saved (fallback)!');
									setTimeout(() => window.location.reload(), 1000);
								}
							} catch (fallbackError) {
								alert('Error saving order: ' + error.message +
									'. Also tried FormData but failed.');
							}
						}
					}
				});
			} else {
				console.warn('SortableJS not loaded');
			}
		});

		document.addEventListener('alpine:init', () => {
			Alpine.data('attributeManager', () => ({
				showAddModal: false,
				showDeleteModal: false,
				selectedAttribute: '',
				deleteAttributeId: null,
				deleteAttributeName: '',
				isDetaching: false,
				toastMessage: '',
				toastType: 'success',
				toastShow: false,
				attachRoute: '{{ route('admin.catalogs.types.attach-attribute', $type) }}',
				detachRoute: '{{ route('admin.catalogs.types.detach-attribute', [$type, '__attribute_id__']) }}',

				showToast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.toastShow = true;
					setTimeout(() => {
						this.toastShow = false;
					}, 3000);
				},

				openAddModal() {
					this.selectedAttribute = '';
					this.showAddModal = true;
				},

				async submitAddForm() {
					if (!this.selectedAttribute) return;
					try {
						const formData = new FormData();
						formData.append('attribute_id', this.selectedAttribute);
						formData.append('_token', '{{ csrf_token() }}');

						const response = await fetch(this.attachRoute, {
							method: 'POST',
							headers: {
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: formData
						});
						const data = await response.json();
						if (data.success) {
							this.showToast(data.message || 'Attribute added!');
							this.showAddModal = false;
							setTimeout(() => window.location.reload(), 1500);
						} else {
							this.showToast(data.message || 'Error', 'error');
						}
					} catch (error) {
						this.showToast('Error', 'error');
					}
				},

				openDeleteModal(id, name) {
					this.deleteAttributeId = id;
					this.deleteAttributeName = name;
					this.showDeleteModal = true;
				},

				async confirmDetach() {
					if (!this.deleteAttributeId) return;
					this.isDetaching = true;
					try {
						const formData = new FormData();
						formData.append('_token', '{{ csrf_token() }}');
						formData.append('_method', 'DELETE');

						const response = await fetch(this.detachRoute.replace('__attribute_id__', this
							.deleteAttributeId), {
							method: 'POST',
							headers: {
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: formData
						});
						const data = await response.json();
						if (data.success) {
							this.showToast(data.message || 'Attribute detached!');
							this.showDeleteModal = false;
							this.isDetaching = false;
							setTimeout(() => window.location.reload(), 1500);
						} else {
							this.showToast(data.message || 'Error', 'error');
							this.isDetaching = false;
						}
					} catch (error) {
						this.showToast('Error', 'error');
						this.isDetaching = false;
					}
				}
			}));
		});

		function attachAttribute(attributeId, attributeName) {
			if (confirm('Add "' + attributeName + '" to this catalog type?')) {
				var form = document.createElement('form');
				form.method = 'POST';
				form.action = '{{ route('admin.catalogs.types.attach-attribute', $type) }}';
				var csrfToken = document.createElement('input');
				csrfToken.type = 'hidden';
				csrfToken.name = '_token';
				csrfToken.value = '{{ csrf_token() }}';
				form.appendChild(csrfToken);
				var attributeInput = document.createElement('input');
				attributeInput.type = 'hidden';
				attributeInput.name = 'attribute_id';
				attributeInput.value = attributeId;
				form.appendChild(attributeInput);
				document.body.appendChild(form);
				form.submit();
			}
		}
	</script>
@endpush
