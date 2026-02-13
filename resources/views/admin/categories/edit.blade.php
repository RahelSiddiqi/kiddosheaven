@extends('admin.layouts.app')

@section('title', 'Edit Category — Admin')

@section('content')
	<div x-data="categoryEditForm()">
		{{-- Header --}}
		<x-admin.ui.entity-header title="Edit Category" :subtitle="'Editing: ' . $category->name" :breadcrumbs="[
		    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
		    ['label' => 'Categories', 'url' => route('admin.categories.index')],
		    ['label' => $category->name, 'url' => route('admin.categories.show', $category)],
		    ['label' => 'Edit'],
		]" :backUrl="route('admin.categories.show', $category)" />

		<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
			@csrf
			@method('PUT')

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				{{-- Main Form --}}
				<div class="lg:col-span-2 space-y-6">
					{{-- Basic Information --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
						</div>
						<div class="p-5 space-y-4">
							{{-- Name --}}
							<div>
								<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
									Category Name <span class="text-error-500">*</span>
								</label>
								<input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
									class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-brand-400"
									placeholder="e.g., Electronics, Clothing, Books" required>
								@error('name')
									<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
								@enderror
							</div>

							{{-- Description --}}
							<div>
								<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
									Description
								</label>
								<textarea name="description" id="description" rows="3"
								 class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-brand-400"
								 placeholder="Brief description of this category">{{ old('description', $category->description) }}</textarea>
								@error('description')
									<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
								@enderror
							</div>

							{{-- Parent Category --}}
							<div>
								<label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
									Parent Category
								</label>
								<select name="parent_id" id="parent_id"
									class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400">
									<option value="">None (Top Level)</option>
									@foreach ($categories as $cat)
										@if ($cat->id !== $category->id && $cat->parent_id !== $category->id)
											<option value="{{ $cat->id }}"
												{{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
												{{ $cat->name }}
											</option>
											@foreach ($cat->children as $child)
												@if ($child->id !== $category->id && $child->parent_id !== $category->id)
													<option value="{{ $child->id }}"
														{{ old('parent_id', $category->parent_id) == $child->id ? 'selected' : '' }}>
														&nbsp;&nbsp;└─ {{ $child->name }}
													</option>
												@endif
											@endforeach
										@endif
									@endforeach
								</select>
								@error('parent_id')
									<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
								@enderror
							</div>

							{{-- Icon --}}
							<div>
								<label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
									Icon (Emoji)
								</label>
								<input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
									class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-brand-400"
									placeholder="📱 🎮 👕 📚" maxlength="50">
								@error('icon')
									<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
								@enderror
							</div>
						</div>
					</div>

					{{-- Settings --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settings</h3>
						</div>
						<div class="p-5 space-y-4">
							{{-- Is Active --}}
							<div class="flex items-center justify-between">
								<div>
									<label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">
										Active Status
									</label>
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Inactive categories won't be visible on the store
									</p>
								</div>
								<label class="relative inline-flex items-center cursor-pointer">
									<input type="checkbox" name="is_active" id="is_active" value="1"
										{{ old('is_active', $category->is_active) ? 'checked' : '' }} class="sr-only peer">
									<div
										class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
									</div>
								</label>
							</div>

							{{-- Show on Home --}}
							<div class="flex items-center justify-between">
								<div>
									<label for="show_on_home" class="text-sm font-medium text-gray-900 dark:text-white">
										Show on Homepage
									</label>
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Display this category on the store homepage
									</p>
								</div>
								<label class="relative inline-flex items-center cursor-pointer">
									<input type="checkbox" name="show_on_home" id="show_on_home" value="1"
										{{ old('show_on_home', $category->show_on_home) ? 'checked' : '' }} class="sr-only peer">
									<div
										class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
									</div>
								</label>
							</div>
						</div>
					</div>
				</div>

				{{-- Sidebar --}}
				<div class="space-y-6">
					{{-- Actions --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
						</div>
						<div class="p-5 space-y-3">
							<button type="submit"
								class="block w-full px-4 py-2.5 text-center text-sm font-medium text-white bg-brand-500 hover:bg-brand-600 rounded-lg transition-colors shadow-theme-xs">
								Update Category
							</button>
							<a href="{{ route('admin.categories.show', $category) }}"
								class="block w-full px-4 py-2.5 text-center text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 rounded-lg transition-colors">
								Cancel
							</a>
						</div>
					</div>

					{{-- Category Info --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Info</h3>
						</div>
						<div class="p-5 space-y-3 text-sm">
							<div>
								<label class="text-gray-500 dark:text-gray-400">Products</label>
								<p class="text-gray-900 dark:text-white font-medium">{{ $category->products->count() }}</p>
							</div>
							<div>
								<label class="text-gray-500 dark:text-gray-400">Subcategories</label>
								<p class="text-gray-900 dark:text-white font-medium">{{ $category->children->count() }}</p>
							</div>
							<div>
								<label class="text-gray-500 dark:text-gray-400">Created</label>
								<p class="text-gray-900 dark:text-white">{{ $category->created_at->format('M d, Y') }}</p>
							</div>
							<div>
								<label class="text-gray-500 dark:text-gray-400">Last Updated</label>
								<p class="text-gray-900 dark:text-white">{{ $category->updated_at->format('M d, Y') }}</p>
							</div>
						</div>
					</div>

					{{-- Help --}}
					<div
						class="rounded-2xl border border-blue-light-200 bg-blue-light-50 dark:border-blue-light-800 dark:bg-blue-light-500/10">
						<div class="p-5 space-y-2">
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-blue-light-600 dark:text-blue-light-400 flex-shrink-0 mt-0.5" fill="none"
									stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								<div class="text-sm text-blue-light-700 dark:text-blue-light-300">
									<p class="font-medium mb-1">Tips:</p>
									<ul class="space-y-1 text-blue-light-600 dark:text-blue-light-400">
										<li>• Use clear, descriptive names</li>
										<li>• Organize with parent categories</li>
										<li>• Add emojis for visual appeal</li>
										<li>• Keep descriptions concise</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function categoryEditForm() {
				return {
					init() {
						// Form is ready
					}
				}
			}
		</script>
	@endpush
@endsection
