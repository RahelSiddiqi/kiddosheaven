@extends('admin.layouts.app')

@section('title', 'Product Attributes — Kiddo\'s Heaven')

@section('content')
	<div x-data="attributeManager()" x-init="init()"
		@toast.window="toast($event.detail.message, $event.detail.type || 'success')">

		<!-- Toast Notification -->
		<div x-show="showToast" x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
			x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
			x-transition:leave-end="opacity-0 translate-y-2" x-cloak
			class="fixed top-4 right-4 z-99999 px-5 py-3.5 rounded-xl shadow-lg text-white flex items-center gap-3 min-w-72"
			:class="toastType === 'error' ? 'bg-red-500' : 'bg-green-500'" style="display: none;">
			<template x-if="toastType !== 'error'">
				<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
				</svg>
			</template>
			<template x-if="toastType === 'error'">
				<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
				</svg>
			</template>
			<span class="text-sm font-medium" x-text="toastMessage"></span>
		</div>

		<!-- Stats Cards -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
			<x-admin.ui.stat-card title="Total Attributes" :value="$attributes->count()" icon="tag" color="blue" />
			@php
				$withValues = 0;
				foreach ($attributes as $attr) {
				    if ($attr->values && $attr->values->count() > 0) {
				        $withValues++;
				    }
				}
				$variantCount = $attributes->where('use_for_variants', true)->count();
			@endphp
			<x-admin.ui.stat-card title="Required Fields" :value="$attributes->where('is_required', true)->count()" icon="alert" color="red" />
			<x-admin.ui.stat-card title="Filterable" :value="$attributes->where('is_filterable', true)->count()" icon="trending" color="purple" />
			<x-admin.ui.stat-card title="Variant-Enabled" :value="$variantCount" icon="layers" color="indigo" />
			<x-admin.ui.stat-card title="With Values" :value="$withValues" icon="box" color="orange" />
		</div>

		<!-- Attributes List -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] pt-4">
			<!-- Header -->
			<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product Attributes</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Click values to edit inline • Drag to reorder</p>
				</div>
				<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
					<button type="button" @click="openCreateModal()"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
						Add Attribute
					</button>
				</div>
			</div>

			<div class="px-5 pb-5 space-y-4">
				@forelse($attributes as $attribute)
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-4"
						x-data="valueManager({{ $attribute->id }}, {{ $attribute->values ? $attribute->values->toJson() : '[]' }})">
						<div class="flex items-center justify-between mb-4">
							<div class="flex-1">
								<div class="flex items-center gap-2 flex-wrap">
									<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $attribute->name }}</h3>
									<span class="px-2 py-0.5 text-xs rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
										{{ ucfirst($attribute->type) }}
									</span>
									@if ($attribute->is_required)
										<span class="px-2 py-0.5 text-xs rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
											Required
										</span>
									@endif
									@if ($attribute->is_filterable)
										<span
											class="px-2 py-0.5 text-xs rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
											Filterable
										</span>
									@endif
									@if ($attribute->use_for_variants)
										<span
											class="px-2 py-0.5 text-xs rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
											Variant
										</span>
									@endif
								</div>
								@if ($attribute->description)
									<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $attribute->description }}</p>
								@endif
								<p class="mt-1 flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500" x-show="values.length > 0">
									<span x-text="values.length">{{ $attribute->values ? $attribute->values->count() : 0 }}</span> values · Click to
									edit · Drag to reorder
								</p>
							</div>
							<div class="flex items-center gap-1">
								<button type="button" @click="openAddValueModal()"
									class="p-2 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:text-green-400 dark:hover:bg-green-900/20 transition-colors"
									title="Add Value">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
									</svg>
								</button>
								<button type="button"
									@click="$dispatch('open-attr-modal', { id: {{ $attribute->id }}, name: '{{ addslashes($attribute->name) }}', type: '{{ $attribute->type }}', is_required: {{ $attribute->is_required ? 'true' : 'false' }}, is_filterable: {{ $attribute->is_filterable ? 'true' : 'false' }}, use_for_variants: {{ $attribute->use_for_variants ? 'true' : 'false' }}, description: '{{ addslashes($attribute->description ?? '') }}' })"
									class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
									title="Edit Attribute">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
									</svg>
								</button>
								<button type="button"
									@click="$dispatch('open-delete-attr', { id: {{ $attribute->id }}, name: '{{ addslashes($attribute->name) }}' })"
									class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
									title="Delete Attribute">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
									</svg>
								</button>
							</div>
						</div>

						<!-- Values Section -->
						<div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">

							<template x-if="values.length > 0">
								<div class="flex flex-wrap gap-2" x-ref="valueContainer">
									<template x-for="(val, index) in values" :key="val.id">
										<div :data-value-id="val.id"
											class="value-chip group relative inline-flex items-center gap-2 rounded-lg border transition-all duration-150"
											:class="val.editing ?
											    'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20 shadow-md ring-2 ring-blue-200 dark:ring-blue-800 px-2 py-1' :
											    'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-3 py-1.5 cursor-move hover:border-blue-300 dark:hover:border-blue-600'">

											<!-- Drag Handle (only when not editing) -->
											<svg x-show="!val.editing" class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 shrink-0" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
											</svg>

											<!-- Display Mode -->
											<div x-show="!val.editing" @click="startEdit(index)"
												class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors select-none">
												<span x-text="val.value"></span>
											</div>

											<!-- Edit Mode -->
											<template x-if="val.editing">
												<div class="flex items-center gap-1.5">
													<input x-model="val.value" @keydown.enter.prevent="saveValue(index)"
														@keydown.escape.prevent="cancelEdit(index)"
														class="text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-2.5 py-1 w-36 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 placeholder:text-gray-400" />
													<button type="button" @click="saveValue(index)"
														class="p-1 rounded-md text-green-600 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-900/30 transition-colors"
														title="Save (Enter)">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
														</svg>
													</button>
													<button type="button" @click="cancelEdit(index)"
														class="p-1 rounded-md text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
														title="Cancel (Esc)">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
														</svg>
													</button>
												</div>
											</template>

											<!-- Delete Button (only when not editing) -->
											<button x-show="!val.editing" type="button" @click.stop="openDeleteValueModal(index)"
												class="opacity-0 group-hover:opacity-100 transition-opacity p-0.5 rounded hover:bg-red-100 dark:hover:bg-red-900/30"
												title="Delete value">
												<svg class="w-3.5 h-3.5 text-red-400 hover:text-red-600 dark:hover:text-red-400" fill="none"
													stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
												</svg>
											</button>
										</div>
									</template>
								</div>
							</template>

							<template x-if="values.length === 0">
								<div class="text-center py-6 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
									<svg class="mx-auto w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
									</svg>
									<p class="text-sm text-gray-400 dark:text-gray-500 mb-2">No values added yet</p>
									<button type="button" @click="openAddValueModal()"
										class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
										+ Add First Value
									</button>
								</div>
							</template>
						</div>

						<!-- Add Value Modal (per-attribute) -->
						<div x-show="showAddValueModal" x-cloak @keydown.escape.window="showAddValueModal && (showAddValueModal = false)"
							class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" style="display: none;">
							<div @click="showAddValueModal = false" class="fixed inset-0 h-full w-full bg-black/50 transition-opacity">
							</div>
							<div @click.stop
								class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6 shadow-xl"
								x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
								x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
								x-transition:leave-start="opacity-100 transform scale-100"
								x-transition:leave-end="opacity-0 transform scale-95">

								<!-- Close Button -->
								<button @click="showAddValueModal = false"
									class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path
											d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
											fill="currentColor" />
									</svg>
								</button>

								<div class="mb-5">
									<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Value</h3>
									<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new value to this attribute</p>
								</div>

								<form @submit.prevent="submitAddValue()">
									<div class="mb-5">
										<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Value *</label>
										<input type="text" x-model="newValueText" x-ref="addValueInput" required placeholder="Enter value..."
											class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800" />
									</div>
									<div class="flex items-center justify-end gap-3">
										<button type="button" @click="showAddValueModal = false"
											class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
											Cancel
										</button>
										<button type="submit"
											class="h-10.5 inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
											Add Value
										</button>
									</div>
								</form>
							</div>
						</div>

						<!-- Delete Value Confirmation Modal -->
						<div x-show="showDeleteValueModal" x-cloak
							@keydown.escape.window="showDeleteValueModal && (showDeleteValueModal = false)"
							class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" style="display: none;">
							<div @click="showDeleteValueModal = false" class="fixed inset-0 h-full w-full bg-black/50 transition-opacity">
							</div>
							<div @click.stop
								class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6 shadow-xl"
								x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
								x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
								x-transition:leave-start="opacity-100 transform scale-100"
								x-transition:leave-end="opacity-0 transform scale-95">
								<div class="text-center">
									<div
										class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
										<svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
										</svg>
									</div>
									<h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Delete Value</h3>
									<p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this value?</p>
									<p class="mb-6 text-sm font-semibold text-gray-900 dark:text-white"
										x-text="deleteValueName ? '&quot;' + deleteValueName + '&quot;' : ''"></p>
									<div class="flex items-center justify-center gap-3">
										<button @click="showDeleteValueModal = false"
											class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
											Cancel
										</button>
										<button @click="confirmDeleteValue()"
											class="h-10.5 inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-700">
											Delete
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3 p-12 text-center">
						<svg class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
						</svg>
						<h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-2">No attributes yet</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first attribute</p>
						<button type="button" @click="openCreateModal()"
							class="h-10.5 inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
							<svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
							</svg>
							Add First Attribute
						</button>
					</div>
				@endforelse
			</div>
		</div>

		<!-- ===== Create/Edit Attribute Modal ===== -->
		<div x-show="showModal" x-cloak @keydown.escape.window="if(showModal) closeModal()"
			class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" style="display: none;">
			<div @click="closeModal()" class="fixed inset-0 h-full w-full bg-black/50 transition-opacity"></div>
			<div @click.stop
				class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6 shadow-xl"
				x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
				x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
				x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">

				<!-- Close Button -->
				<button @click="closeModal()"
					class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path
							d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
							fill="currentColor" />
					</svg>
				</button>

				<div class="mb-5">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white"
						x-text="formData.id ? 'Edit Attribute' : 'Add New Attribute'"></h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1"
						x-text="formData.id ? 'Update the attribute details below' : 'Fill in the details to create a new attribute'"></p>
				</div>

				<form @submit.prevent="saveAttribute()">
					<div class="space-y-5">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attribute Name *</label>
							<input type="text" x-model="formData.name" required placeholder="e.g., Color, Size, Material"
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800" />
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
							<select x-model="formData.type"
								class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 appearance-none">
								<option value="text">Text</option>
								<option value="select">Select (Dropdown)</option>
								<option value="multiselect">Multi-Select</option>
								<option value="color">Color</option>
								<option value="number">Number</option>
								<option value="boolean">Boolean</option>
								<option value="date">Date</option>
							</select>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
							<textarea x-model="formData.description" rows="2" placeholder="Optional description for this attribute..."
							 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"></textarea>
						</div>

						<div class="flex gap-6 flex-wrap">
							<label class="relative inline-flex items-center cursor-pointer">
								<input type="checkbox" x-model="formData.is_required" class="sr-only peer">
								<div
									class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
								</div>
								<span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Required</span>
							</label>
							<label class="relative inline-flex items-center cursor-pointer">
								<input type="checkbox" x-model="formData.is_filterable" class="sr-only peer">
								<div
									class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
								</div>
								<span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Filterable</span>
							</label>
							<label class="relative inline-flex items-center cursor-pointer">
								<input type="checkbox" x-model="formData.use_for_variants" class="sr-only peer">
								<div
								class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
								</div>
								<span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Use for variants</span>
							</label>
						</div>
					</div>

					<div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-200 dark:border-gray-700">
						<button type="button" @click="closeModal()"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							Cancel
						</button>
						<button type="submit" :disabled="isSaving"
							class="h-10.5 inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed">
							<svg x-show="isSaving" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
								</circle>
								<path class="opacity-75" fill="currentColor"
									d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
								</path>
							</svg>
							<span x-text="formData.id ? 'Update Attribute' : 'Create Attribute'"></span>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- ===== Delete Attribute Confirmation Modal ===== -->
		<div x-show="showDeleteModal" x-cloak @keydown.escape.window="if(showDeleteModal) showDeleteModal = false"
			class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" style="display: none;">
			<div @click="showDeleteModal = false" class="fixed inset-0 h-full w-full bg-black/50 transition-opacity"></div>
			<div @click.stop
				class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6 shadow-xl"
				x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
				x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
				x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
				<div class="text-center">
					<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
						<svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					</div>
					<h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Delete Attribute</h3>
					<p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this attribute? This will
						also remove all its values.</p>
					<p class="mb-6 text-sm font-semibold text-gray-900 dark:text-white"
						x-text="deleteAttrName ? '&quot;' + deleteAttrName + '&quot;' : ''"></p>
					<div class="flex items-center justify-center gap-3">
						<button @click="showDeleteModal = false"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							Cancel
						</button>
						<button @click="confirmDeleteAttribute()" :disabled="isDeleting"
							class="h-10.5 inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
							<svg x-show="isDeleting" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
								</circle>
								<path class="opacity-75" fill="currentColor"
									d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
								</path>
							</svg>
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
		<script>
			function attributeManager() {
				return {
					showModal: false,
					showDeleteModal: false,
					showToast: false,
					toastMessage: '',
					toastType: 'success',
					isSaving: false,
					isDeleting: false,
					deleteAttrId: null,
					deleteAttrName: '',
					formData: {
						id: null,
						name: '',
						type: 'text',
						description: '',
						is_required: false,
						is_filterable: false,
						use_for_variants: false
					},

					init() {
						@if (session('success'))
							this.toast('{{ session('success') }}');
						@endif

						// Listen for edit attribute event
						window.addEventListener('open-attr-modal', (e) => {
							this.formData = {
								id: e.detail.id,
								name: e.detail.name,
								type: e.detail.type,
								description: e.detail.description,
								is_required: e.detail.is_required,
								is_filterable: e.detail.is_filterable,
								use_for_variants: e.detail.use_for_variants
							};
							this.showModal = true;
						});

						// Listen for delete attribute event
						window.addEventListener('open-delete-attr', (e) => {
							this.deleteAttrId = e.detail.id;
							this.deleteAttrName = e.detail.name;
							this.showDeleteModal = true;
						});
					},

					openCreateModal() {
						this.resetForm();
						this.showModal = true;
					},

					closeModal() {
						this.showModal = false;
						this.resetForm();
					},

					resetForm() {
						this.formData = {
							id: null,
							name: '',
							type: 'text',
							description: '',
							is_required: false,
							is_filterable: false,
							use_for_variants: false
						};
					},

					async saveAttribute() {
						if (this.isSaving) return;
						this.isSaving = true;

						const url = this.formData.id ?
							`/admin/attributes/${this.formData.id}` :
							'/admin/attributes';
						const method = this.formData.id ? 'PUT' : 'POST';

						try {
							const response = await fetch(url, {
								method: method,
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								},
								body: JSON.stringify(this.formData)
							});

							const data = await response.json();
							if (response.ok && data.success) {
								this.closeModal();
								this.toast(data.message || 'Attribute saved successfully');
								setTimeout(() => window.location.reload(), 1200);
							} else {
								this.toast(data.message || 'Error saving attribute', 'error');
							}
						} catch (error) {
							console.error('Error:', error);
							this.toast('Error saving attribute', 'error');
						}
						this.isSaving = false;
					},

					async confirmDeleteAttribute() {
						if (this.isDeleting || !this.deleteAttrId) return;
						this.isDeleting = true;

						try {
							const response = await fetch(`/admin/attributes/${this.deleteAttrId}`, {
								method: 'DELETE',
								headers: {
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								}
							});

							const data = await response.json();
							if (response.ok && data.success) {
								this.showDeleteModal = false;
								this.toast(data.message || 'Attribute deleted successfully');
								setTimeout(() => window.location.reload(), 1200);
							} else {
								this.showDeleteModal = false;
								this.toast(data.message || 'Error deleting attribute', 'error');
							}
						} catch (error) {
							console.error('Error:', error);
							this.showDeleteModal = false;
							this.toast('Error deleting attribute', 'error');
						}
						this.isDeleting = false;
					},

					toast(message, type = 'success') {
						this.toastMessage = message;
						this.toastType = type;
						this.showToast = true;
						setTimeout(() => this.showToast = false, 3000);
					}
				};
			}

			function valueManager(attributeId, initialValues) {
				return {
					attributeId: attributeId,
					values: initialValues.map(v => ({
						...v,
						editing: false,
						originalValue: v.value
					})),
					sortable: null,
					showAddValueModal: false,
					showDeleteValueModal: false,
					newValueText: '',
					deleteValueIndex: null,
					deleteValueName: '',

					init() {
						this.$nextTick(() => {
							const container = this.$refs.valueContainer;
							if (container && typeof Sortable !== 'undefined') {
								this.sortable = Sortable.create(container, {
									animation: 150,
									ghostClass: 'opacity-50',
									filter: 'input, button',
									preventOnFilter: false,
									onEnd: () => this.saveOrder()
								});
							}
						});
					},

					openAddValueModal() {
						this.newValueText = '';
						this.showAddValueModal = true;
						this.$nextTick(() => {
							const input = this.$refs.addValueInput;
							if (input) input.focus();
						});
					},

					async submitAddValue() {
						if (!this.newValueText.trim()) return;

						try {
							const response = await fetch(`/admin/attributes/${this.attributeId}/values`, {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								},
								body: JSON.stringify({
									value: this.newValueText.trim()
								})
							});
							const data = await response.json();
							if (response.ok && data.success) {
								this.showAddValueModal = false;
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'success',
										message: 'Value added successfully'
									}
								}));
								setTimeout(() => window.location.reload(), 1200);
							} else {
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'error',
										message: data.message || 'Error adding value'
									}
								}));
							}
						} catch (error) {
							console.error('Error:', error);
							window.dispatchEvent(new CustomEvent('toast', {
								detail: {
									type: 'error',
									message: 'Error adding value'
								}
							}));
						}
					},

					startEdit(index) {
						this.values.forEach((v, i) => {
							if (i !== index && v.editing) {
								v.value = v.originalValue;
								v.editing = false;
							}
						});
						this.values[index].originalValue = this.values[index].value;
						this.values[index].editing = true;
						this.$nextTick(() => {
							const inputs = this.$el.querySelectorAll('input[x-model="val.value"]');
							for (const input of inputs) {
								if (input.offsetParent !== null) {
									input.focus();
									input.select();
									break;
								}
							}
						});
					},

					cancelEdit(index) {
						this.values[index].value = this.values[index].originalValue;
						this.values[index].editing = false;
					},

					async saveValue(index) {
						const val = this.values[index];
						if (!val.value.trim()) {
							this.cancelEdit(index);
							return;
						}
						val.editing = false;
						val.originalValue = val.value;

						try {
							const response = await fetch(`/admin/attributes/${this.attributeId}/values/${val.id}`, {
								method: 'PUT',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								},
								body: JSON.stringify({
									value: val.value
								})
							});
							if (response.ok) {
								const data = await response.json();
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'success',
										message: data.message || 'Value updated successfully'
									}
								}));
							} else {
								const data = await response.json().catch(() => ({}));
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'error',
										message: data.message || 'Error saving value'
									}
								}));
							}
						} catch (error) {
							console.error('Error:', error);
							window.dispatchEvent(new CustomEvent('toast', {
								detail: {
									type: 'error',
									message: 'Error saving value'
								}
							}));
						}
					},

					openDeleteValueModal(index) {
						this.deleteValueIndex = index;
						this.deleteValueName = this.values[index].value;
						this.showDeleteValueModal = true;
					},

					async confirmDeleteValue() {
						const index = this.deleteValueIndex;
						if (index === null) return;

						const val = this.values[index];
						try {
							const response = await fetch(`/admin/attributes/${this.attributeId}/values/${val.id}`, {
								method: 'DELETE',
								headers: {
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								}
							});
							const data = await response.json();
							if (response.ok && data.success) {
								this.values.splice(index, 1);
								this.showDeleteValueModal = false;
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'success',
										message: data.message || 'Value deleted'
									}
								}));
							} else {
								this.showDeleteValueModal = false;
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'error',
										message: data.message || 'Error deleting value'
									}
								}));
							}
						} catch (error) {
							console.error('Error:', error);
							this.showDeleteValueModal = false;
							window.dispatchEvent(new CustomEvent('toast', {
								detail: {
									type: 'error',
									message: 'Error deleting value'
								}
							}));
						}
					},

					async saveOrder() {
						const container = this.$refs.valueContainer;
						if (!container) return;
						const order = Array.from(container.querySelectorAll('.value-chip')).map(el => el.dataset.valueId)
							.filter(id => id);
						if (order.length === 0) return;

						try {
							const response = await fetch(`/admin/attributes/${this.attributeId}/values/reorder`, {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								},
								body: JSON.stringify({
									order: order
								})
							});
							if (response.ok) {
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'success',
										message: 'Order saved'
									}
								}));
							} else {
								const data = await response.json().catch(() => ({}));
								window.dispatchEvent(new CustomEvent('toast', {
									detail: {
										type: 'error',
										message: data.message || 'Error saving order'
									}
								}));
							}
						} catch (error) {
							console.error('Error:', error);
							window.dispatchEvent(new CustomEvent('toast', {
								detail: {
									type: 'error',
									message: 'Error saving order'
								}
							}));
						}
					}
				};
			}
		</script>
	@endpush
@endsection
