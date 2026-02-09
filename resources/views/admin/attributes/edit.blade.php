@extends('admin.layouts.app')

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

		<!-- Edit Form -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Attribute</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update attribute details</p>
					</div>
				</div>

				<div class="p-5">
					<form id="attribute-edit-form" action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST"
						autocomplete="off">
						@csrf
						@method('PUT')

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attribute Name</label>
							<input type="text" name="name" value="{{ old('name', $attribute->name) }}" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								placeholder="Enter attribute name">
							@error('name')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
							<select name="type" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="text" {{ $attribute->type === 'text' ? 'selected' : '' }}>Text</option>
								<option value="select" {{ $attribute->type === 'select' ? 'selected' : '' }}>Select (Single)</option>
								<option value="multiselect" {{ $attribute->type === 'multiselect' ? 'selected' : '' }}>Multi-Select</option>
								<option value="boolean" {{ $attribute->type === 'boolean' ? 'selected' : '' }}>Yes/No (Boolean)</option>
								<option value="number" {{ $attribute->type === 'number' ? 'selected' : '' }}>Number</option>
								<option value="date" {{ $attribute->type === 'date' ? 'selected' : '' }}>Date</option>
							</select>
							@error('type')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
							<textarea name="description" rows="3"
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
							 placeholder="Optional description">{{ old('description', $attribute->description) }}</textarea>
							@error('description')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="grid grid-cols-2 gap-4 mb-6">
							<div class="flex items-center">
								<label class="flex items-center cursor-pointer">
									<input type="checkbox" name="is_required" value="1" {{ $attribute->is_required ? 'checked' : '' }}
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
									<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Required</span>
								</label>
							</div>
							<div class="flex items-center">
								<label class="flex items-center cursor-pointer">
									<input type="checkbox" name="is_filterable" value="1" {{ $attribute->is_filterable ? 'checked' : '' }}
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
									<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Filterable</span>
								</label>
							</div>
						</div>

						<div class="flex justify-end gap-3">
							<a href="{{ route('admin.attributes.index') }}"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</a>
							<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
								Update Attribute
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Sidebar Info -->
		<div class="col-span-12 lg:col-span-4">
			<!-- Current Values -->
			@if (in_array($attribute->type, ['select', 'multiselect']))
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6">
					<div class="flex flex-col gap-4 p-5 border-b border-gray-200 dark:border-gray-700">
						<div class="flex items-center justify-between">
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Values</h3>
							<span
								class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
								{{ $attribute->values->count() }}
							</span>
						</div>
					</div>
					<div class="p-5">
						@if ($attribute->values->count() > 0)
							<ul class="space-y-2">
								@foreach ($attribute->values as $value)
									<li class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
										<span class="text-sm text-gray-700 dark:text-gray-300">{{ $value->value }}</span>
										<form action="{{ route('admin.attributes.values.destroy', $value->id) }}" method="POST" class="inline">
											@csrf
											@method('DELETE')
											<button type="submit" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded">
												<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
												</svg>
											</button>
										</form>
									</li>
								@endforeach
							</ul>
						@else
							<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No values added yet</p>
						@endif

						<!-- Add Value Form -->
						<form action="{{ route('admin.attributes.values.store', $attribute->id) }}" method="POST" class="mt-4">
							@csrf
							<div class="flex gap-2">
								<input type="text" name="value" required placeholder="Add new value"
									class="flex-1 h-9 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-blue-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<button type="submit"
									class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
									Add
								</button>
							</div>
						</form>
					</div>
				</div>
			@endif

			<!-- Assigned Catalogs -->
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex flex-col gap-4 p-5 border-b border-gray-200 dark:border-gray-700">
					<div class="flex items-center justify-between">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Used In Catalogs</h3>
						<span
							class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400">
							{{ $attribute->catalogs->count() }}
						</span>
					</div>
				</div>
				<div class="p-5">
					@if ($attribute->catalogs->count() > 0)
						<ul class="space-y-2">
							@foreach ($attribute->catalogs as $catalog)
								<li class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
									<span class="text-sm text-gray-700 dark:text-gray-300">{{ $catalog->name }}</span>
									<a href="{{ route('admin.catalogs.show', $catalog->id) }}"
										class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded">
										<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
										</svg>
									</a>
								</li>
							@endforeach
						</ul>
					@else
						<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Not used in any catalog</p>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
