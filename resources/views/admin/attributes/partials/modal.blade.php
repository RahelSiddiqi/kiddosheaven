<!-- Create/Edit Modal -->
<div id="attribute-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
	<div class="flex min-h-screen items-center justify-center p-4">
		<div class="fixed inset-0 bg-black/50 transition-opacity cursor-pointer" onclick="closeModal()"></div>
		<div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-50">
			<form id="attribute-form" onsubmit="saveAttribute(event)">
				@csrf
				<input type="hidden" id="attribute-id" name="id">
				<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" id="modal-title">Add New Attribute</h3>
				</div>
				<div class="p-6 space-y-4">
					<div>
						<label for="attr-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Attribute Name
							*</label>
						<input type="text" id="attr-name" name="name" required
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
							placeholder="e.g., Color, Size, Material">
					</div>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<label for="attr-type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Input Type
								*</label>
							<select id="attr-type" name="type" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="text">Text</option>
								<option value="select">Dropdown (Single)</option>
								<option value="multiselect">Dropdown (Multiple)</option>
								<option value="number">Number</option>
								<option value="boolean">Yes/No (Checkbox)</option>
								<option value="date">Date</option>
							</select>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Options</label>
							<div class="flex items-center gap-4 h-11">
								<label class="flex items-center gap-2 cursor-pointer">
									<input type="checkbox" id="attr-required" name="is_required" value="1"
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700">
									<span class="text-sm text-gray-700 dark:text-gray-400">Required</span>
								</label>
								<label class="flex items-center gap-2 cursor-pointer">
									<input type="checkbox" id="attr-filterable" name="is_filterable" value="1"
										class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700">
									<span class="text-sm text-gray-700 dark:text-gray-400">Filterable</span>
								</label>
							</div>
						</div>
					</div>
					<div id="initial-values-container" class="hidden">
						<label for="attr-initial-values" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Initial
							Values (one per line)</label>
						<textarea id="attr-initial-values" name="initial_values" rows="4"
						 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
						 placeholder="Red&#10;Blue&#10;Green"></textarea>
					</div>
					<div>
						<label for="attr-description"
							class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
						<textarea id="attr-description" name="description" rows="2"
						 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
						 placeholder="Brief description of this attribute"></textarea>
					</div>
				</div>
				<div
					class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
					<button type="button" onclick="closeModal()"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
						Cancel
					</button>
					<button type="submit"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:hover:bg-blue-500/80">
						<span id="modal-submit-text">Save Attribute</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
