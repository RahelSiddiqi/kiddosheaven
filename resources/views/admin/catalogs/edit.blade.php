@extends('admin.layouts.app')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Back Button & Header -->
		<div class="col-span-12">
			<div class="flex items-center gap-4 mb-6">
				<a href="{{ route('admin.catalogs.index') }}"
					class="flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Catalogs
				</a>
			</div>
		</div>

		<!-- Edit Form -->
		<div class="col-span-12 lg:col-span-6">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Catalog</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update catalog details</p>
					</div>
				</div>

				<div class="p-5">
					<form action="{{ route('admin.catalogs.update', $catalog->id) }}" method="POST">
						@csrf
						@method('PUT')

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catalog Name</label>
							<input type="text" name="name" value="{{ old('name', $catalog->name) }}" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								placeholder="Enter catalog name">
							@error('name')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catalog Type</label>
							<select name="type" id="catalogTypeSelect" required
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">Select type</option>
								@foreach (\App\Models\CatalogType::getAllOptions() as $slug => $name)
									<option value="{{ $slug }}" {{ $catalog->type === $slug ? 'selected' : '' }}>{{ $name }}
									</option>
								@endforeach
							</select>
							@error('type')
								<p class="text-sm text-red-500 mt-1">{{ $message }}</p>
							@enderror
						</div>

						<div class="mb-4">
							<label class="flex items-center cursor-pointer">
								<input type="checkbox" name="show_on_home" value="1" {{ $catalog->show_on_home ? 'checked' : '' }}
									class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
								<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Show on Home Page</span>
							</label>
						</div>

						<div class="flex justify-end gap-3">
							<a href="{{ route('admin.catalogs.index') }}"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
								Cancel
							</a>
							<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
								Update Catalog
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Quick Stats -->
		<div class="col-span-12 lg:col-span-6">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex flex-col gap-4 p-5 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Quick Stats</h3>
				</div>
				<div class="p-5">
					<div class="grid grid-cols-2 gap-4">
						<div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10">
							<p class="text-sm text-gray-500 dark:text-gray-400">Products</p>
							<p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $catalog->products_count ?? 0 }}</p>
						</div>
						<div class="p-4 rounded-xl bg-green-50 dark:bg-green-500/10">
							<p class="text-sm text-gray-500 dark:text-gray-400">Attributes</p>
							<p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $catalog->attributes->count() ?? 0 }}</p>
						</div>
					</div>

					<div class="mt-6">
						<a href="{{ route('admin.catalogs.show', $catalog->id) }}"
							class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
							</svg>
							View Full Details
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Available Attributes Section -->
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Select Attributes</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
							Choose which attributes from the catalog type to enable for this catalog
						</p>
					</div>
					@if ($catalog->type)
						<a href="{{ route('admin.catalogs.types.attributes', $catalog->type) }}"
							class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
							</svg>
							Manage {{ $catalog->type->name }} Attributes
						</a>
					@endif
				</div>

				<div class="p-5">
					@if ($catalog->type && $catalog->type->attributes->count() > 0)
						<form action="{{ route('admin.catalogs.update-attributes', $catalog->id) }}" method="POST" id="attributesForm">
							@csrf
							@method('PUT')

							<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
								@foreach ($catalog->type->attributes as $attribute)
									@php
										$isEnabled = $catalog->attributes->contains($attribute->id);
									@endphp
									<label
										class="relative flex items-start p-4 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-800
                                    {{ $isEnabled ? 'border-blue-300 bg-blue-50/50 dark:border-blue-600 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-700' }}">
										<input type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ $isEnabled ? 'checked' : '' }}
											class="w-5 h-5 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
										<div class="ml-3">
											<span class="block text-sm font-medium text-gray-800 dark:text-white/90">{{ $attribute->name }}</span>
											<span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ ucfirst($attribute->type) }}</span>
											@if ($attribute->pivot->is_required)
												<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
													Required
												</span>
											@endif
										</div>
									</label>
								@endforeach
							</div>

							<div class="mt-6 flex justify-end">
								<button type="submit" form="attributesForm"
									class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
									Save Attribute Selection
								</button>
							</div>
						</form>
					@elseif(!$catalog->type)
						<div class="text-center py-8">
							<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
								</svg>
							</div>
							<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-1">Select a Catalog Type</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400">
								Please select a catalog type above to see available attributes.
							</p>
						</div>
					@else
						<div class="text-center py-8">
							<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
								</svg>
							</div>
							<h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-1">No Attributes Available</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
								The selected catalog type has no attributes defined yet.
							</p>
							<a href="{{ route('admin.catalogs.types.index') }}"
								class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400">
								Manage Catalog Types
							</a>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
