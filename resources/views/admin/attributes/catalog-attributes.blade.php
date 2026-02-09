@extends('admin.layouts.app')

@section('title', "Manage {$catalog->name} Attributes — Kiddo's Heaven")

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
		<div class="flex items-center gap-3">
			<a href="{{ route('admin.attributes.index') }}"
				class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
				<svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
				</svg>
			</a>
			<div>
				<h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $catalog->name }} Attributes</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage attributes for this catalog</p>
			</div>
		</div>
		<div class="flex items-center gap-3">
			<a href="{{ route('admin.catalogs.index') }}"
				class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
				<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
				</svg>
				All Catalogs
			</a>
		</div>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Current Catalog Attributes -->
		<div class="lg:col-span-2 space-y-4">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
					<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Assigned Attributes</h2>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Drag to reorder attributes</p>
				</div>
				<div class="p-4">
					@if ($catalogAttributes->count() > 0)
						<div class="overflow-x-auto">
							<table class="w-full text-left border-collapse">
								<thead>
									<tr class="border-b border-gray-200 dark:border-gray-700">
										<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10">
										</th>
										<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Attribute
										</th>
										<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
										<th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Required
										</th>
										<th
											class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">
											Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="sortable-catalog-attributes">
									@foreach ($catalogAttributes as $attr)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $attr->id }}">
											<td class="px-4 py-3">
												<svg class="w-5 h-5 text-gray-400 drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
												</svg>
											</td>
											<td class="px-4 py-3">
												<span class="font-medium text-gray-800 dark:text-white/90">{{ $attr->name }}</span>
												@if ($attr->description)
													<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($attr->description, 40) }}</p>
												@endif
											</td>
											<td class="px-4 py-3">
												<span
													class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
													{{ ucfirst($attr->type) }}
												</span>
											</td>
											<td class="px-4 py-3">
												@if ($attr->pivot && $attr->pivot->is_required)
													<span
														class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
														Required
													</span>
												@else
													<span class="text-xs text-gray-400">Optional</span>
												@endif
											</td>
											<td class="px-4 py-3 text-right">
												<form action="{{ route('admin.catalogs.attributes.detach', [$catalog, $attr]) }}" method="POST"
													class="inline">
													@csrf
													@method('DELETE')
													<button type="submit"
														class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 bg-white text-red-600 hover:bg-red-50 dark:bg-gray-800 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20"
														title="Detach Attribute" onclick="return confirm('Are you sure you want to detach this attribute?')">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
														</svg>
													</button>
												</form>
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
									d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
							</svg>
							<p class="text-gray-500 dark:text-gray-400 mb-2">No attributes assigned yet</p>
							<p class="text-sm text-gray-400 dark:text-gray-500">Add attributes from the list on the right</p>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Available Attributes -->
		<div class="lg:col-span-1">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
					<h2 class="text-base font-medium text-gray-800 dark:text-white/90">Add Attribute</h2>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select attributes to add</p>
				</div>
				<div class="p-4">
					<form action="{{ route('admin.catalogs.attributes.attach', $catalog) }}" method="POST">
						@csrf
						<div class="space-y-3 mb-4">
							<div>
								<label for="attribute_id"
									class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Attribute</label>
								<select id="attribute_id" name="attribute_id" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">Select attribute</option>
									@foreach ($attributes as $attr)
										@if (!$catalogAttributes->contains('id', $attr->id))
											<option value="{{ $attr->id }}">{{ $attr->name }} ({{ ucfirst($attr->type) }})</option>
										@endif
									@endforeach
								</select>
							</div>
							<div>
								<label class="flex items-center gap-2 cursor-pointer">
									<input type="checkbox" id="is_required" name="is_required" value="1"
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700">
									<span class="text-sm text-gray-700 dark:text-gray-400">Required for products in this catalog</span>
								</label>
							</div>
						</div>
						<button type="submit"
							class="w-full h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
							<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
							</svg>
							Add Attribute
						</button>
					</form>

					@if ($attributes->whereNotIn('id', $catalogAttributes->pluck('id')->toArray())->count() == 0)
						<div class="mt-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
							<p class="text-sm text-blue-700 dark:text-blue-400 text-center">
								All attributes are already assigned to this catalog!
							</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Info Card -->
			<div class="mt-4 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
					<h2 class="text-base font-medium text-gray-800 dark:text-white/90">How It Works</h2>
				</div>
				<div class="p-4 space-y-3">
					<div class="flex items-start gap-3">
						<div
							class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
							<span class="text-xs font-medium text-blue-600 dark:text-blue-400">1</span>
						</div>
						<p class="text-sm text-gray-600 dark:text-gray-400">Add attributes to this catalog from the list above</p>
					</div>
					<div class="flex items-start gap-3">
						<div
							class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
							<span class="text-xs font-medium text-blue-600 dark:text-blue-400">2</span>
						</div>
						<p class="text-sm text-gray-600 dark:text-gray-400">Drag to reorder the display order</p>
					</div>
					<div class="flex items-start gap-3">
						<div
							class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
							<span class="text-xs font-medium text-blue-600 dark:text-blue-400">3</span>
						</div>
						<p class="text-sm text-gray-600 dark:text-gray-400">Required attributes must be filled when creating products</p>
					</div>
					<div class="flex items-start gap-3">
						<div
							class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
							<span class="text-xs font-medium text-blue-600 dark:text-blue-400">4</span>
						</div>
						<p class="text-sm text-gray-600 dark:text-gray-400">Products in this catalog will show these dynamic fields</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const tbody = document.getElementById('sortable-catalog-attributes');
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

							fetch('{{ route('admin.catalogs.attributes.reorder', [$catalog]) }}', {
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
