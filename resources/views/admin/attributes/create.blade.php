@extends('admin.layouts.app')

@section('title', 'Create Attribute — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Back Button & Header -->
		<div class="col-span-12">
			<div class="flex items-center gap-4 mb-6">
				<a href="{{ route('admin.attributes.index') }}"
					class="flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Attributes
				</a>
			</div>
		</div>

		<!-- Create Form -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Create New Attribute</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new product attribute</p>
					</div>
				</div>

				<div class="p-5">
					<form action="{{ route('admin.attributes.store') }}" method="POST" autocomplete="off">
						@csrf

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attribute Name <span
									class="text-red-500">*</span></label>
							<input type="text" name="name" value="{{ old('name') }}" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								placeholder="e.g., Color, Size, Material">
							@error('name')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type <span
									class="text-red-500">*</span></label>
							<select name="type" id="attribute-type" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
								<option value="select" {{ old('type') === 'select' ? 'selected' : '' }}>Select (Single Choice)</option>
								<option value="multiselect" {{ old('type') === 'multiselect' ? 'selected' : '' }}>Multi-Select (Multiple Choices)
								</option>
								<option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Yes/No (Boolean)</option>
								<option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>Number</option>
								<option value="date" {{ old('type') === 'date' ? 'selected' : '' }}>Date</option>
							</select>
							@error('type')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4" id="initial-values-container" style="display: none;">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Values
								<span class="text-gray-500 text-xs">(one per line)</span>
							</label>
							<textarea name="initial_values" rows="5"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							 placeholder="Red&#10;Blue&#10;Green&#10;Yellow">{{ old('initial_values') }}</textarea>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Add default options for select fields (optional). You can
								add more values later.</p>
						</div>

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
							<textarea name="description" rows="3"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							 placeholder="Optional description for this attribute">{{ old('description') }}</textarea>
							@error('description')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="grid grid-cols-2 gap-4 mb-6">
							<div class="flex items-center">
								<label class="flex items-center cursor-pointer">
									<input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
									<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Required</span>
								</label>
								<span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(Must be filled)</span>
							</div>
							<div class="flex items-center">
								<label class="flex items-center cursor-pointer">
									<input type="checkbox" name="is_filterable" value="1" {{ old('is_filterable') ? 'checked' : '' }}
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
									<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Filterable</span>
								</label>
								<span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(Show in filters)</span>
							</div>
						</div>

						<div class="flex justify-end gap-3">
							<a href="{{ route('admin.attributes.index') }}"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</a>
							<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
								Create Attribute
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Sidebar Info -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="p-5 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Attribute Types</h3>
				</div>
				<div class="p-5">
					<ul class="space-y-3">
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Text</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Free form text input</p>
								</div>
							</div>
						</li>
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Select</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Single choice dropdown</p>
								</div>
							</div>
						</li>
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Multi-Select</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Multiple choice selection</p>
								</div>
							</div>
						</li>
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Boolean</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Yes/No toggle</p>
								</div>
							</div>
						</li>
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Number</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Numeric values only</p>
								</div>
							</div>
						</li>
						<li>
							<div class="flex items-start gap-2">
								<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div>
									<p class="text-sm font-medium text-gray-800 dark:text-white/90">Date</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Date picker</p>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		// Show/hide initial values textarea based on attribute type
		document.getElementById('attribute-type').addEventListener('change', function() {
			const container = document.getElementById('initial-values-container');
			if (this.value === 'select' || this.value === 'multiselect') {
				container.style.display = 'block';
			} else {
				container.style.display = 'none';
			}
		});

		// Trigger on page load for old values
		document.addEventListener('DOMContentLoaded', function() {
			const typeSelect = document.getElementById('attribute-type');
			if (typeSelect.value === 'select' || typeSelect.value === 'multiselect') {
				document.getElementById('initial-values-container').style.display = 'block';
			}
		});
	</script>
@endpush
