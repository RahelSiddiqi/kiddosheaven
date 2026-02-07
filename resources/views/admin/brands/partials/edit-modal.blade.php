<!-- Edit Brand Modal -->
<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
	<div class="flex min-h-screen items-center justify-center p-4">
		<div x-show="showEditModal" @click="$dispatch('close-edit-modal')"
			class="fixed inset-0 bg-black/50 transition-opacity cursor-pointer"></div>
		<div
			class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 shadow-xl z-50"
			x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
			x-transition:enter-end="opacity-100 scale-100">
			<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Brand</h3>
				<button @click="$dispatch('close-edit-modal')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>
			<form id="editBrandForm" @submit.prevent="updateBrand()" class="p-5 space-y-4">
				@csrf
				@method('PUT')
				{{-- Validation Errors --}}
				<div x-show="formErrors" x-transition
					class="p-4 mb-4 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800"
					style="display: none;">
					<ul class="text-sm text-red-600 dark:text-red-400">
						<template x-for="(errors, field) in formErrors">
							<template x-for="error in errors">
								<li class="flex items-center gap-2">
									<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd"
											d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
											clip-rule="evenodd" />
									</svg>
									<span x-text="error"></span>
								</li>
							</template>
						</template>
					</ul>
				</div>
				<div>
					<label for="edit_brand_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
					<input type="text" name="name" id="edit_brand_name" required
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
				</div>
				<div>
					<label for="edit_brand_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
					<input type="text" name="slug" id="edit_brand_slug"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
				</div>
				<div>
					<label for="edit_brand_description"
						class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
					<textarea name="description" id="edit_brand_description" rows="3"
					 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"></textarea>
				</div>
				<div>
					<label for="edit_brand_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo *</label>
					<input type="file" name="logo" id="edit_brand_logo" accept="image/*"
						class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Supported formats: JPG, PNG, GIF, SVG. Leave empty to keep
						current logo.</p>
				</div>
				<div>
					<label class="flex items-center cursor-pointer">
						<input type="hidden" name="is_active" value="0">
						<input type="checkbox" name="is_active" id="edit_brand_is_active" value="1"
							class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
						<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
					</label>
				</div>
				<div class="flex justify-end gap-3 pt-4">
					<button type="button" @click="$dispatch('close-edit-modal')"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Cancel
					</button>
					<button type="submit" :disabled="isSubmitting"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
						<span x-show="!isSubmitting">Save Changes</span>
						<span x-show="isSubmitting" class="flex items-center gap-2">
							<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
								viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor"
									d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
								</path>
							</svg>
							Saving...
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
