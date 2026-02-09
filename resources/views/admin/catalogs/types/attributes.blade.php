@extends('admin.layouts.app')

@section('title', 'Manage Attributes for ' . $catalog_type->name . ' — Kiddo\'s Heaven')

@push('styles')
	<style>
		.drag-handle {
			cursor: grab;
		}

		.drag-handle:active {
			cursor: grabbing;
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
				Manage Attributes: {{ $catalog_type->name }}
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
			<button type="button" onclick="document.getElementById('addAttributeModal').classList.add('show')"
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

	<div class="grid grid-cols-12 gap-6">
		<!-- Assigned Attributes -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Assigned Attributes</h3>
					<div class="flex items-center gap-2">
						<button type="button"
							class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
							onclick="selectAllAttributes()">
							Select All
						</button>
						<span class="text-gray-300 dark:text-gray-600">|</span>
						<button type="button" class="text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
							onclick="detachSelectedAttributes()">
							Detach Selected
						</button>
					</div>
				</div>
				<div class="p-5">
					@if ($typeAttributes->count() > 0)
						<div class="overflow-x-auto">
							<table class="w-full whitespace-nowrap">
								<thead>
									<tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
										<th class="px-4 py-3">
											<input type="checkbox"
												class="form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
												id="select-all-attributes" onchange="toggleAllAttributes()">
										</th>
										<th class="px-4 py-3">Attribute</th>
										<th class="px-4 py-3">Type</th>
										<th class="px-4 py-3">Required</th>
										<th class="px-4 py-3">Order</th>
										<th class="px-4 py-3 text-right">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($typeAttributes as $attribute)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
											<td class="px-4 py-3">
												<input type="checkbox"
													class="form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 attribute-checkbox">
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
											<td class="px-4 py-3">
												<span class="text-sm text-gray-600 dark:text-gray-400">{{ $attribute->pivot->sort_order }}</span>
											</td>
											<td class="px-4 py-3 text-right">
												<button type="button"
													onclick="detachAttribute({{ $attribute->id }}, '{{ addslashes($attribute->name) }}')"
													class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 bg-white text-red-600 hover:bg-red-50 dark:bg-gray-800 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20"
													title="Detach">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
													</svg>
												</button>
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
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
								Add attributes from the list below to assign them to this catalog type.
							</p>
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
					<p class="text-sm text-gray-500 dark:text-gray-400">
						Click to add attributes to this catalog type
					</p>
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
											class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-green-200 bg-white text-green-600 hover:bg-green-50 dark:bg-gray-800 dark:border-green-800 dark:text-green-400 dark:hover:bg-green-900/20"
											title="Attach">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
											</svg>
										</button>
									</div>
								@endif
							@endforeach
						</div>
						@if ($typeAttributes->count() == $allAttributes->count())
							<div class="text-center py-8">
								<p class="text-sm text-gray-500 dark:text-gray-400">
									All attributes have been assigned to this catalog type.
								</p>
							</div>
						@endif
					@else
						<div class="text-center py-8">
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
								No attributes available.
							</p>
							<a href="{{ route('admin.attributes.index') }}"
								class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
								Create Attribute
							</a>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Add Attribute Modal -->
	<div id="addAttributeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
		role="dialog" aria-modal="true">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div class="fixed inset-0 bg-gray-900/50 transition-opacity" onclick="closeAddModal()"></div>
			<div
				class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 text-left shadow-xl transition-all">
				<div class="flex items-center justify-between mb-4">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" id="modal-title">Add Attribute</h3>
					<button type="button" onclick="closeAddModal()"
						class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form action="{{ route('admin.catalogs.types.attach-attribute', $type) }}" method="POST" id="add-attribute-form">
					@csrf
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Attribute</label>
						<select name="attribute_id" required
							class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">Choose an attribute...</option>
							@foreach ($allAttributes as $attribute)
								@if (!$typeAttributes->contains('id', $attribute->id))
									<option value="{{ $attribute->id }}">{{ $attribute->name }} ({{ ucfirst($attribute->type) }})</option>
								@endif
							@endforeach
						</select>
					</div>
					<div class="flex justify-end gap-3">
						<button type="button" onclick="closeAddModal()"
							class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
							Add Attribute
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		// Close modal function
		function closeAddModal() {
			document.getElementById('addAttributeModal').classList.add('hidden');
		}

		function openAddModal() {
			document.getElementById('addAttributeModal').classList.remove('hidden');
		}

		// Attach attribute
		function attachAttribute(attributeId, attributeName) {
			if (confirm('Are you sure you want to add "' + attributeName + '" to this catalog type?')) {
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

		// Detach attribute
		function detachAttribute(attributeId, attributeName) {
			if (confirm('Are you sure you want to detach "' + attributeName + '" from this catalog type?')) {
				var form = document.createElement('form');
				form.method = 'POST';
				form.action = '{{ route('admin.catalogs.types.detach-attribute', [$type, '__attribute_id__']) }}'
					.replace('__attribute_id__', attributeId);

				var csrfToken = document.createElement('input');
				csrfToken.type = 'hidden';
				csrfToken.name = '_token';
				csrfToken.value = '{{ csrf_token() }}';
				form.appendChild(csrfToken);

				var methodInput = document.createElement('input');
				methodInput.type = 'hidden';
				methodInput.name = '_method';
				methodInput.value = 'DELETE';
				form.appendChild(methodInput);

				document.body.appendChild(form);
				form.submit();
			}
		}

		// Toggle all checkboxes
		function toggleAllAttributes() {
			var masterCheckbox = document.getElementById('select-all-attributes');
			var checkboxes = document.querySelectorAll('.attribute-checkbox');
			checkboxes.forEach(function(checkbox) {
				checkbox.checked = masterCheckbox.checked;
			});
		}
	</script>
@endpush
