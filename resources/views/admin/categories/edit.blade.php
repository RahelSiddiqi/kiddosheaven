@extends('admin.layouts.app')

@section('title', 'Edit Category — Admin')

@section('content')
	@if (session('success'))
		<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2800)"
			class="fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white flex items-center gap-2 min-w-64"
			style="display: none;">
			<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<span class="text-sm font-medium">{{ session('success') }}</span>
		</div>
	@endif

	@if ($errors->any())
		<div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
			<ul class="list-disc pl-5 space-y-1">
				@foreach ($errors->all() as $error)
					<li class="text-sm text-red-700 dark:text-red-400">{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@php
		$headerActions = [
		    [
		        'label' => 'Save',
		        'url' => null,
		        'style' => 'primary',
		        'icon' =>
		            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
		        'onclick' => "event.preventDefault(); document.getElementById('category-edit-form').requestSubmit();",
		        'title' => 'Save changes',
		    ],
		    [
		        'label' => 'Cancel',
		        'url' => route('admin.categories.show', $category),
		        'style' => 'ghost',
		        'icon' =>
		            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>',
		    ],
		];

		$attributeOptions = \App\Models\ProductAttribute::select('id', 'name', 'type', 'use_for_variants')
		    ->orderBy('name')
		    ->get();
		$selectedAttributeIds = old('attribute_ids', $category->attributes->pluck('id')->toArray());

		$renderCategoryOptions = function ($items, $prefix = '') use (&$renderCategoryOptions, $category) {
		    foreach ($items as $item) {
		        echo '<option value="' .
		            $item->id .
		            '" ' .
		            (old('parent_id', $category->parent_id) == $item->id ? 'selected' : '') .
		            '>' .
		            $prefix .
		            e($item->name) .
		            '</option>';
		        if ($item->children && $item->children->isNotEmpty()) {
		            $renderCategoryOptions($item->children, $prefix . '— ');
		        }
		    }
		};
	@endphp

	<div x-data="categoryEditForm()">
		<x-admin.ui.entity-header title="Edit Category" :subtitle="'Editing: ' . $category->name" :breadcrumbs="[
		    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
		    ['label' => 'Categories', 'url' => route('admin.categories.index')],
		    ['label' => $category->name, 'url' => route('admin.categories.show', $category)],
		    ['label' => 'Edit'],
		]" :backUrl="route('admin.categories.show', $category)"
			:actions="$headerActions" />

		<form id="category-edit-form" method="POST" action="{{ route('admin.categories.update', $category) }}"
			class="space-y-6">
			@csrf
			@method('PUT')

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="lg:col-span-2 space-y-6">
					{{-- Basic Information --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
						</div>
						<div class="p-5 space-y-4">
							<div class="space-y-1">
								<label for="name" class="text-sm font-medium text-gray-900 dark:text-white">Name</label>
								<input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
									class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>

							<div class="space-y-1">
								<label for="description" class="text-sm font-medium text-gray-900 dark:text-white">Description</label>
								<textarea id="description" name="description" rows="3"
								 class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description', $category->description) }}</textarea>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div class="space-y-1">
									<label for="icon" class="text-sm font-medium text-gray-900 dark:text-white">Icon (emoji or class)</label>
									<input type="text" id="icon" name="icon" value="{{ old('icon', $category->icon) }}"
										class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
								</div>
								<div class="space-y-1">
									<label for="parent_id" class="text-sm font-medium text-gray-900 dark:text-white">Parent Category</label>
									<select id="parent_id" name="parent_id"
										class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
										<option value="">No Parent</option>
										@php($renderCategoryOptions($categories))
									</select>
								</div>
							</div>
						</div>
					</div>

					{{-- Attributes --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attributes</h3>
							<div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
								<span
									class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300">Variant:
									{{ $attributeOptions->where('use_for_variants', true)->count() }}</span>
								<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Other:
									{{ $attributeOptions->where('use_for_variants', false)->count() }}</span>
							</div>
						</div>
						<div class="p-5 space-y-4" x-data="attributeSelect({
	    all: @js(
    $attributeOptions->map(
        fn($attr) => [
            'id' => $attr->id,
            'name' => $attr->name,
            'type' => $attr->type,
        ],
    ),
),
	    selected: @js($selectedAttributeIds)
	})">
							<div class="space-y-2">
								<label class="text-sm font-medium text-gray-900 dark:text-white">Selected Attributes</label>
								<div class="relative" @click.outside="isOpen = false">
									<div
										class="min-h-[42px] w-full cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-2 flex flex-wrap items-center gap-2 dark:border-gray-700 dark:bg-gray-800"
										@click="isOpen = !isOpen">
										<div class="flex flex-wrap gap-2" x-show="selectedIds.length">
											<template x-for="attr in selectedAttributes" :key="attr.id">
												<span
													class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-200">
													<span x-text="attr.name"></span>
													<span
														class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300"
														x-text="attr.type"></span>
													<button type="button" class="text-gray-400 hover:text-gray-700" @click.stop="remove(attr.id)">
														<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
														</svg>
													</button>
												</span>
											</template>
										</div>
										<p class="text-sm text-gray-500" x-show="!selectedIds.length">Select attributes</p>
										<svg class="ml-auto w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
										</svg>
									</div>

									<div x-show="isOpen" x-transition.origin.top.left
										class="absolute z-20 mt-2 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
										<div class="relative px-3 py-2 border-b border-gray-200 dark:border-gray-800">
											<svg class="w-4 h-4 text-gray-400 absolute left-4 top-3" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
											</svg>
											<input type="text" x-model="query" placeholder="Search attributes..."
												class="block w-full pl-9 pr-3 py-2 text-sm rounded-md border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-gray-900 dark:text-white" />
										</div>

										<div class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
											<template x-for="opt in filteredOptions" :key="opt.id">
												<button type="button"
													class="w-full flex items-center justify-between px-4 py-3 text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-800"
													@click="toggle(opt.id)">
													<div>
														<p class="font-medium text-gray-900 dark:text-white" x-text="opt.name"></p>
														<p class="text-xs text-gray-500 dark:text-gray-400" x-text="opt.type"></p>
													</div>
													<div>
														<span x-show="selectedIds.includes(opt.id)"
															class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-500 text-white text-[10px]">✓</span>
													</div>
												</button>
											</template>
										</div>
									</div>
								</div>
							</div>

							<template x-for="id in selectedIds" :key="id">
								<input type="hidden" name="attribute_ids[]" :value="id">
							</template>
						</div>
					</div>
				</div>

				<div class="space-y-6">
					{{-- Settings --}}
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settings</h3>
						</div>
						<div class="p-5 space-y-4">
							<div class="flex items-center justify-between">
								<div>
									<label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">Active Status</label>
									<p class="text-sm text-gray-500 dark:text-gray-400">Inactive categories won't be visible on the store</p>
								</div>
								<label class="relative inline-flex items-center cursor-pointer">
									<input type="checkbox" name="is_active" id="is_active" value="1"
										{{ old('is_active', $category->is_active) ? 'checked' : '' }} class="sr-only peer">
									<div
										class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
									</div>
								</label>
							</div>

							<div class="flex items-center justify-between">
								<div>
									<label for="show_on_home" class="text-sm font-medium text-gray-900 dark:text-white">Show on Homepage</label>
									<p class="text-sm text-gray-500 dark:text-gray-400">Display this category on the store homepage</p>
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
@endsection

@push('scripts')
	<script>
		function categoryEditForm() {
			return {};
		}

		function attributeSelect(config) {
			return {
				allOptions: config.all || [],
				selectedIds: [...(config.selected || [])],
				query: '',
				isOpen: false,
				get filteredOptions() {
					const q = this.query.trim().toLowerCase();
					if (!q) {
						return this.allOptions;
					}
					return this.allOptions.filter(opt => opt.name.toLowerCase().includes(q));
				},
				get selectedAttributes() {
					return this.allOptions.filter(opt => this.selectedIds.includes(opt.id));
				},
				toggle(id) {
					if (this.selectedIds.includes(id)) {
						this.selectedIds = this.selectedIds.filter(item => item !== id);
						return;
					}
					this.selectedIds.push(id);
				},
				remove(id) {
					this.selectedIds = this.selectedIds.filter(item => item !== id);
				},
			};
		}
	</script>
@endpush
