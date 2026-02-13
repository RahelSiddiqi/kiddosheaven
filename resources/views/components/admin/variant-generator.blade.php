{{-- Variant Generator Modal Component --}}
<div x-data="variantGenerator()" x-cloak>
	{{-- Trigger Button --}}
	<button @click="openModal()" type="button"
		class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
		<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
		</svg>
		Generate Variants
	</button>

	{{-- Modal --}}
	<div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
		style="display: none;">

		<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
			{{-- Background overlay --}}
			<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

			{{-- Modal panel --}}
			<div
				class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

				{{-- Header --}}
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
					<div class="flex items-center justify-between">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
							Generate Product Variants
						</h3>
						<button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
						Select attributes and values to automatically create all variant combinations
					</p>
				</div>

				{{-- Body --}}
				<div class="px-6 py-4 max-h-96 overflow-y-auto">
					{{-- Attributes Selection --}}
					<div class="space-y-4">
						<template x-for="(attribute, index) in selectedAttributes" :key="index">
							<div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
								<div class="flex items-center justify-between mb-3">
									<h4 class="font-medium text-gray-900 dark:text-white" x-text="attribute.name"></h4>
									<button @click="removeAttribute(index)" class="text-red-600 hover:text-red-700 text-sm">
										Remove
									</button>
								</div>

								{{-- Values --}}
								<div class="flex flex-wrap gap-2">
									<template x-for="value in attribute.values" :key="value.id">
										<label class="inline-flex items-center px-3 py-1.5 rounded-md cursor-pointer transition-colors"
											:class="value.selected ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' :
											    'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'">
											<input type="checkbox" x-model="value.selected" @change="updatePreview()" class="sr-only">
											<span class="text-sm font-medium" x-text="value.value"></span>
										</label>
									</template>
								</div>
							</div>
						</template>

						{{-- Add Attribute Dropdown --}}
						<div>
							<select @change="addAttribute($event.target.value); $event.target.value = ''"
								class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
								<option value="">+ Add Attribute (Color, Size, etc.)</option>
								<template x-for="attr in availableAttributes" :key="attr.id">
									<option :value="attr.id" x-text="attr.name"></option>
								</template>
							</select>
						</div>
					</div>

					{{-- Preview: select which combinations to create (e.g. Small+Red, Small+Green only, not full matrix) --}}
					<div x-show="variantsPreview.length > 0"
						class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
						<h4 class="font-medium text-blue-900 dark:text-blue-100 mb-1">
							<span x-text="selectedCombinationCount"></span> of <span x-text="variantsPreview.length"></span> combinations will be created
						</h4>
						<p class="text-xs text-blue-700 dark:text-blue-300 mb-2">Uncheck any combination you don’t want (e.g. only Small: Red/Green/Blue, Medium: Red, Large: Blue/Yellow).</p>
						<div class="max-h-40 overflow-y-auto space-y-1">
							<template x-for="(combo, idx) in variantsPreview" :key="idx">
								<label class="flex items-center gap-2 cursor-pointer hover:bg-blue-100/50 dark:hover:bg-blue-900/30 px-2 py-1 rounded">
									<input type="checkbox" x-model="combo.selected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
									<span class="text-sm text-gray-800 dark:text-gray-200" x-text="combo.label"></span>
								</label>
							</template>
						</div>
					</div>
				</div>

				{{-- Footer --}}
				<div
					class="px-6 py-4 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
					<div class="text-sm text-gray-600 dark:text-gray-400">
						<span x-show="variantsPreview.length > 0">
							<span x-text="selectedCombinationCount"></span> variant<span x-show="selectedCombinationCount !== 1">s</span> will be
							created
						</span>
						<span x-show="variantsPreview.length === 0">
							Select attributes and values to continue
						</span>
					</div>
					<div class="flex gap-3">
						<button @click="closeModal()" type="button"
							class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
							Cancel
						</button>
						<button @click="generateVariants()" type="button" :disabled="selectedCombinationCount === 0 || generating"
							:class="variantsPreview.length === 0 || generating ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
							class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg transition-colors">
							<span x-show="!generating">Generate Variants</span>
							<span x-show="generating">Generating...</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function variantGenerator() {
		return {
			showModal: false,
			generating: false,
			allAttributes: @json($variantAttributes ?? []),
			selectedAttributes: [],
			variantsPreview: [],
			productId: {{ $product->id ?? 'null' }},

			get availableAttributes() {
				const selectedIds = this.selectedAttributes.map(a => a.id);
				return this.allAttributes.filter(a => !selectedIds.includes(a.id));
			},

			openModal() {
				this.showModal = true;
				this.selectedAttributes = [];
				this.variantsPreview = [];
			},

			closeModal() {
				this.showModal = false;
				this.generating = false;
			},

			addAttribute(attributeId) {
				if (!attributeId) return;

				const attribute = this.allAttributes.find(a => a.id == attributeId);
				if (!attribute) return;

				// Add attribute with selectable values
				this.selectedAttributes.push({
					id: attribute.id,
					name: attribute.name,
					values: attribute.values.map(v => ({
						id: v.id,
						value: v.value,
						selected: false
					}))
				});

				this.updatePreview();
			},

			removeAttribute(index) {
				this.selectedAttributes.splice(index, 1);
				this.updatePreview();
			},

			updatePreview() {
				const selectedData = this.selectedAttributes
					.map(attr => ({
						id: attr.id,
						name: attr.name,
						values: attr.values.filter(v => v.selected).map(v => ({ id: v.id, value: v.value }))
					}))
					.filter(attr => attr.values.length > 0);

				if (selectedData.length === 0) {
					this.variantsPreview = [];
					return;
				}

				// Generate combinations with labels and value_ids for custom selection
				this.variantsPreview = this.generateCombinationsWithIds(selectedData);
			},

			generateCombinationsWithIds(attributes, index = 0, current = [], currentIds = []) {
				if (index >= attributes.length) {
					return [{ label: current.join(' / '), valueIds: [...currentIds], selected: true }];
				}

				const results = [];
				const currentAttr = attributes[index];

				for (const value of currentAttr.values) {
					results.push(...this.generateCombinationsWithIds(attributes, index + 1, [...current, value.value], [...currentIds, value.id]));
				}

				return results;
			},

			get selectedCombinationCount() {
				if (!Array.isArray(this.variantsPreview) || this.variantsPreview.length === 0) return 0;
				return this.variantsPreview.filter(c => c.selected).length;
			},

			async generateVariants() {
				if (!Array.isArray(this.variantsPreview) || this.variantsPreview.length === 0) return;

				const selected = this.variantsPreview.filter(c => c.selected);
				if (selected.length === 0) {
					alert('Please select at least one combination to create.');
					return;
				}

				this.generating = true;

				// Prepare data for API
				const attributesData = this.selectedAttributes
					.map(attr => ({
						attribute_id: attr.id,
						value_ids: attr.values.filter(v => v.selected).map(v => v.id)
					}))
					.filter(attr => attr.value_ids.length > 0);

				const body = { attributes: attributesData };
				// If user deselected some combinations, send only the selected ones
				if (selected.length < this.variantsPreview.length) {
					body.custom_combinations = selected.map(c => c.valueIds);
				}

				try {
					const response = await fetch(`/admin/products/${this.productId}/variants/generate`, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify(body)
					});

					const data = await response.json();

					if (data.success) {
						// Show success message
						alert(data.message);
						// Reload page to show new variants
						window.location.reload();
					} else {
						alert('Error: ' + (data.message || 'Failed to generate variants'));
					}
				} catch (error) {
					console.error('Error:', error);
					alert('Failed to generate variants. Please try again.');
				} finally {
					this.generating = false;
				}
			}
		}
	}
</script>
