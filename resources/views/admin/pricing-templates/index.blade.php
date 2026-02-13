@extends('admin.layouts.app')

@section('title', 'Pricing Templates — Kiddo\'s Heaven')

@php
	use Illuminate\Support\Str;
@endphp

@section('content')
	<div x-data="pricingTemplateManager()" x-init="init()" class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 space-y-4">
			<div class="relative">
				<div x-show="showToast" x-transition
					class="pointer-events-none absolute right-6 top-3 z-50 rounded-xl border px-4 py-3 text-sm font-semibold text-white shadow-lg"
					:class="toastType === 'error' ? 'border-red-200 bg-red-500/90' : 'border-emerald-200 bg-emerald-500/90'" x-cloak>
					<span x-text="toastMessage"></span>
				</div>
			</div>

			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.01]">
				<div
					class="flex flex-col gap-3 border-b border-gray-100 px-6 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Define how we calculate selling prices for different product
							groups.</p>
					</div>
					<div class="flex flex-wrap items-center gap-2">
						<button type="button" @click="openCreateModal()"
							class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-theme-xs transition hover:border-gray-400 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-900/80 dark:text-gray-200 dark:hover:border-gray-500">
							<svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							<span>Create Template</span>
						</button>
					</div>
				</div>

				@if ($templates->isEmpty())
					<div class="space-y-3 px-6 py-8 text-sm text-gray-500 dark:text-gray-400">
						<p>No pricing templates yet. Each template lets you reuse consistent markup logic across categories.</p>
						<button type="button" @click="openCreateModal()"
							class="inline-flex items-center gap-2 rounded-lg border border-dashed border-gray-300 px-4 py-2 text-sm font-semibold text-blue-600 hover:border-blue-400 hover:text-blue-700 dark:border-gray-600 dark:text-blue-400">
							Add first template
						</button>
					</div>
				@else
					<div class="overflow-hidden">
						<div class="max-w-full overflow-x-auto px-6 py-5">
							<table class="min-w-full text-sm">
								<thead>
									<tr
										class="border-y border-gray-100 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:text-gray-400">
										<th class="px-3 py-3">Name</th>
										<th class="px-3 py-3">Strategy</th>
										<th class="px-3 py-3">Summary</th>
										<th class="px-3 py-3">Categories</th>
										<th class="px-3 py-3">Status</th>
										<th class="px-3 py-3 text-right">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
									@foreach ($templates as $template)
										@php
											$templatePayload = [
											    'id' => $template->id,
											    'name' => $template->name,
											    'description' => $template->description,
											    'strategy_type' => $template->strategy_type,
											    'config' => $template->config,
											    'is_active' => $template->is_active,
											    'is_global' => $template->is_global,
											];
										@endphp
										<tr x-data="{ template: @js($templatePayload) }" class="text-gray-700 dark:text-white/90">
											<td class="px-3 py-4 align-top">
												<div class="font-semibold text-gray-900 dark:text-white">{{ $template->name }}</div>
												<p class="text-xs text-gray-500 dark:text-gray-400">
													{{ Str::limit($template->description ?? 'No description', 60) }}</p>
											</td>
											<td class="px-3 py-4 align-top text-sm font-semibold text-gray-600 dark:text-gray-300">
												{{ $template->strategy_name }}</td>
											<td class="px-3 py-4 align-top text-sm text-gray-500 dark:text-gray-400">{{ $template->config_summary }}</td>
											<td class="px-3 py-4 align-top text-sm text-gray-500 dark:text-gray-400">
												<span
													class="inline-flex items-center rounded-full bg-gray-100 px-3 py-0.5 text-[11px] font-semibold text-gray-600 dark:bg-gray-800/70 dark:text-gray-300">
													{{ $template->categories_count }} categories
												</span>
											</td>
											<td class="px-3 py-4 align-top text-sm">
												<div class="flex flex-col gap-1">
													<span
														class="inline-flex items-center gap-1 rounded-full px-3 py-0.5 text-[11px] font-semibold {{ $template->is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-gray-50 text-gray-500 dark:bg-gray-800/60 dark:text-gray-400' }}">
														{{ $template->is_active ? 'Active' : 'Inactive' }}
													</span>
													<span
														class="inline-flex items-center gap-1 rounded-full px-3 py-0.5 text-[11px] font-semibold {{ $template->is_global ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300' : 'bg-gray-50 text-gray-500 dark:bg-gray-800/60 dark:text-gray-400' }}">
														{{ $template->is_global ? 'Global scope' : 'Scoped to categories' }}
													</span>
												</div>
											</td>
											<td class="px-3 py-4 align-top text-right text-sm font-medium">
												<div class="flex flex-wrap items-center justify-end gap-2">
													<button type="button" @click="viewTemplate(template.id)"
														class="group inline-flex h-9 w-9 items-center justify-center rounded-lg  text-gray-600 transition hover:border-gray-300 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-white/5"
														title="Preview template">
														<span class="sr-only" x-text="'Preview ' + template.name"></span>
														<x-icons.list />
													</button>
													<button type="button" @click="editTemplate(template)"
														class="group inline-flex h-9 w-9 items-center justify-center rounded-lg  text-blue-600 transition hover:border-blue-200 hover:bg-blue-100 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500  dark:hover:border-blue-400 dark:hover:bg-blue-400/10"
														title="Edit template">
														<span class="sr-only" x-text="'Edit ' + template.name"></span>
														<x-icons.edit />
													</button>
													<button type="button" @click="openDeleteModal(template)"
														class="group inline-flex h-9 w-9 items-center justify-center rounded-lg  text-red-600 transition hover:border-red-200 hover:bg-red-100 hover:text-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500 dark:hover:border-red-400 dark:hover:bg-red-400/10"
														title="Delete template">
														<span class="sr-only" x-text="'Delete ' + template.name"></span>
														<x-icons.delete />
													</button>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endif
			</div>
		</div>

		<!-- Create / Edit Modal -->
		<div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4 py-8"
			@keydown.escape.window="closeModal()">
			<div class="absolute inset-0" @click="closeModal()"></div>
			<div
				class="relative w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
				<form @submit.prevent="saveTemplate()" class="flex h-full flex-col">
					<div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
						<div>
							<p class="text-xs uppercase tracking-wide text-gray-500">Pricing Templates</p>
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
								<span x-text="formData.id ? 'Edit template' : 'Create template'"></span>
							</h3>
						</div>
						<button type="button"
							class="rounded-full p-1 text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
							@click="closeModal()">
							<span class="sr-only">Close</span>
							<svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M6 6L14 14M6 14L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
						</button>
					</div>
					<div class="space-y-4 px-6 py-5">
						<div>
							<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Template name</label>
							<input type="text" x-model="formData.name" required
								class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
						</div>
						<div>
							<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
							<textarea x-model="formData.description" rows="2"
							 class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
						</div>
						<div>
							<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Pricing strategy</label>
							<select x-model="formData.strategy_type" @change="strategyChanged()"
								class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
								<option value="percentage_markup">Percentage markup</option>
								<option value="fixed_markup">Fixed markup</option>
								<option value="tiered">Tiered pricing</option>
								<option value="attribute_based">Attribute-based rules</option>
							</select>
						</div>
						<div class="space-y-4">
							<div x-show="formData.strategy_type === 'percentage_markup'" class="space-y-1" x-cloak>
								<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Percentage markup</label>
								<input type="number" x-model.number="formData.percentage" min="0" step="0.01"
									class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
								<p class="text-xs text-gray-500 dark:text-gray-400">Multiply cost by (1 + percentage/100).</p>
							</div>
							<div x-show="formData.strategy_type === 'fixed_markup'" class="space-y-1" x-cloak>
								<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Fixed markup</label>
								<input type="number" x-model.number="formData.fixed_amount" min="0" step="0.01"
									class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
								<p class="text-xs text-gray-500 dark:text-gray-400">Adds a fixed amount to the cost.</p>
							</div>
							<div x-show="formData.strategy_type === 'tiered'" class="space-y-3" x-cloak>
								<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Pricing tiers</label>
								<div class="space-y-2">
									<template x-for="(tier, index) in formData.tiers" :key="index">
										<div
											class="grid gap-2 rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-700 dark:bg-gray-900/40 md:grid-cols-3">
											<div>
												<label class="text-xs text-gray-500">Min cost (৳)</label>
												<input type="number" x-model.number="tier.min_cost" min="0" step="0.01"
													class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
											</div>
											<div>
												<label class="text-xs text-gray-500">Markup (%)</label>
												<input type="number" x-model.number="tier.percentage" min="0" step="0.01"
													class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
											</div>
											<div class="flex items-center justify-end">
												<button type="button" @click="formData.tiers.splice(index, 1)"
													class="text-xs font-semibold text-red-600 transition hover:text-red-500">Remove</button>
											</div>
										</div>
									</template>
									<button type="button" @click="addTier()"
										class="w-full rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:text-gray-300">
										+ Add tier
									</button>
								</div>
							</div>
							<div x-show="formData.strategy_type === 'attribute_based'" class="space-y-3" x-cloak>
								<label class="text-sm font-medium text-gray-700 dark:text-gray-200">Attribute rules</label>
								<div class="space-y-3">
									<template x-for="(rule, index) in formData.rules" :key="index">
										<div
											class="rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-700 dark:bg-gray-900/40 space-y-2">
											<div class="flex items-center justify-between">
												<span class="text-xs font-semibold text-gray-500">Rule #<span x-text="index + 1"></span></span>
												<button type="button" @click="formData.rules.splice(index, 1)"
													class="text-xs font-semibold text-red-600 transition hover:text-red-500">Remove</button>
											</div>
											<div class="grid gap-2 md:grid-cols-3">
												<input type="text" x-model="rule.attribute" placeholder="Attribute (e.g. Size)"
													class="rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
												<input type="text" x-model="rule.value" placeholder="Value (e.g. Large)"
													class="rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
												<input type="number" x-model.number="rule.percentage" min="0" step="0.01"
													placeholder="Markup %"
													class="rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
											</div>
										</div>
									</template>
									<button type="button" @click="addRule()"
										class="w-full rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm font-semibold text-gray-600 transition hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:text-gray-300">
										+ Add rule
									</button>
									<div class="space-y-1 pt-2 text-sm text-gray-500 dark:text-gray-400">
										<label class="font-medium text-gray-700 dark:text-gray-200">Default markup (%)</label>
										<input type="number" x-model.number="formData.default_percentage" min="0" step="0.01"
											class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
										<p class="text-xs text-gray-500 dark:text-gray-400">Used when no rule matches.</p>
									</div>
								</div>
							</div>
						</div>
						<div class="flex flex-wrap gap-4">
							<label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
								<input type="checkbox" x-model="formData.is_active"
									class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
								<span>Active</span>
							</label>
							<label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
								<input type="checkbox" x-model="formData.is_global"
									class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
								<span>Global template</span>
							</label>
						</div>
					</div>
					<div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-gray-800">
						<button type="button"
							class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-300 dark:border-gray-700 dark:text-gray-200"
							@click="closeModal()">
							Cancel
						</button>
						<button type="submit"
							class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed"
							:disabled="isSaving">
							<svg x-show="isSaving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
								</circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
							</svg>
							<span x-text="isSaving ? 'Saving…' : (formData.id ? 'Update template' : 'Create template')"></span>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Preview Modal -->
		<div x-cloak x-show="showPreview" class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4 py-6"
			@keydown.escape.window="showPreview = false">
			<div class="absolute inset-0" @click="showPreview = false"></div>
			<div
				class="relative w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900">
				<header class="mb-4 flex items-center justify-between">
					<div>
						<p class="text-xs uppercase tracking-wide text-gray-400">Preview</p>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Price examples</h3>
					</div>
					<button type="button"
						class="rounded-full p-1 text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
						@click="showPreview = false">
						<span class="sr-only">Close preview</span>
						<svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 6L14 14M6 14L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
					</button>
				</header>
				<div class="space-y-3">
					<template x-for="example in previewExamples" :key="example.cost">
						<div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3 text-sm dark:bg-gray-800">
							<div>
								<p class="text-xs text-gray-500">Cost</p>
								<p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="'৳' + Number(example.cost).toFixed(2)">
								</p>
							</div>
							<div class="text-right">
								<p class="text-xs text-gray-500">Calculated</p>
								<p class="text-lg font-semibold text-blue-600 dark:text-blue-300"
									x-text="'৳' + Number(example.price).toFixed(2)"></p>
								<p class="text-[11px] text-gray-500" x-text="'Markup: ৳' + Number(example.markup).toFixed(2)"></p>
							</div>
						</div>
					</template>
				</div>
				<div class="mt-6 text-right">
					<button type="button"
						class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-300 dark:border-gray-700 dark:text-gray-300"
						@click="showPreview = false">
						Close
					</button>
				</div>
			</div>
		</div>

		<!-- Delete Confirmation Modal -->
		<div x-cloak x-show="showDeleteModal"
			class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6"
			@keydown.escape.window="closeDeleteModal()">
			<div class="absolute inset-0" @click="closeDeleteModal()"></div>
			<div
				class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900">
				<header class="mb-4 flex items-center justify-between">
					<div>
						<p class="text-xs uppercase tracking-wide text-gray-400">Delete template</p>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm deletion</h3>
					</div>
					<button type="button"
						class="rounded-full p-1 text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
						@click="closeDeleteModal()">
						<span class="sr-only">Close delete modal</span>
						<svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 6L14 14M6 14L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
					</button>
				</header>
				<p class="text-sm text-gray-500 dark:text-gray-400">
					Are you sure you want to delete
					<span class="font-semibold text-gray-900 dark:text-white" x-text="deleteTarget?.name || 'this template'"></span>?
				</p>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
				<div class="mt-6 flex items-center justify-end gap-3">
					<button type="button"
						class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-300 dark:border-gray-700 dark:text-gray-200"
						@click="closeDeleteModal()">
						Cancel
					</button>
					<button type="button"
						class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
						@click="confirmDelete()" :disabled="isDeletingTemplate">
						<svg x-show="isDeletingTemplate" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
							</circle>
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
						</svg>
						<span x-text="isDeletingTemplate ? 'Deleting…' : 'Delete template'"></span>
					</button>
				</div>
			</div>
		</div>
	</div>
	</div>

@endsection

@push('scripts')
	<script>
		function pricingTemplateManager() {
			const cloneArray = (items = []) => items.map(item => ({
				...item
			}));

			return {
				showModal: false,
				showPreview: false,
				showToast: false,
				toastType: 'success',
				toastMessage: '',
				toastTimeout: null,
				previewExamples: [],
				isSaving: false,
				showDeleteModal: false,
				deleteTarget: null,
				isDeletingTemplate: false,
				formData: {
					id: null,
					name: '',
					description: '',
					strategy_type: 'percentage_markup',
					percentage: 50,
					fixed_amount: 10,
					tiers: [],
					rules: [],
					default_percentage: 50,
					is_active: true,
					is_global: false
				},

				init() {
					@if (session('success'))
						this.toast('{{ session('success') }}', 'success');
					@endif
					this.strategyChanged();
				},

				openCreateModal() {
					this.resetForm();
					this.strategyChanged();
					this.showModal = true;
				},

				editTemplate(template) {
					const config = template.config ?? {};
					this.formData = {
						id: template.id,
						name: template.name,
						description: template.description ?? '',
						strategy_type: template.strategy_type,
						percentage: config.percentage ?? 50,
						fixed_amount: config.fixed_amount ?? 10,
						tiers: cloneArray(config.tiers ?? []),
						rules: cloneArray(config.rules ?? []),
						default_percentage: config.default_percentage ?? 50,
						is_active: template.is_active ?? true,
						is_global: template.is_global ?? false
					};
					this.strategyChanged();
					this.showModal = true;
				},

				async viewTemplate(id) {
					try {
						const response = await fetch(`/admin/pricing-templates/${id}`);
						const data = await response.json();
						if (data.success) {
							this.previewExamples = data.examples ?? [];
							this.showPreview = true;
						} else {
							this.toast(data.message || 'Unable to load preview', 'error');
						}
					} catch (error) {
						console.error(error);
						this.toast('Unable to load preview', 'error');
					}
				},

				openDeleteModal(template) {
					this.deleteTarget = template;
					this.showDeleteModal = true;
				},

				closeDeleteModal() {
					this.showDeleteModal = false;
					this.deleteTarget = null;
					this.isDeletingTemplate = false;
				},

				async confirmDelete() {
					if (!this.deleteTarget?.id || this.isDeletingTemplate) {
						return;
					}

					this.isDeletingTemplate = true;
					try {
						const response = await fetch(`/admin/pricing-templates/${this.deleteTarget.id}`, {
							method: 'DELETE',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': this.getCsrfToken()
							}
						});

						const data = await response.json();
						if (data.success) {
							this.toast(data.message || 'Template deleted', 'success');
							this.closeDeleteModal();
							setTimeout(() => window.location.reload(), 900);
						} else {
							this.toast(data.message || 'Unable to delete template', 'error');
						}
					} catch (error) {
						console.error(error);
						this.toast('Unable to delete template', 'error');
					} finally {
						this.isDeletingTemplate = false;
					}
				},

				closeModal() {
					this.showModal = false;
					this.resetForm();
				},

				resetForm() {
					this.formData = {
						id: null,
						name: '',
						description: '',
						strategy_type: 'percentage_markup',
						percentage: 50,
						fixed_amount: 10,
						tiers: [],
						rules: [],
						default_percentage: 50,
						is_active: true,
						is_global: false
					};
					this.isSaving = false;
				},

				strategyChanged() {
					if (this.formData.strategy_type === 'tiered') {
						if (!this.formData.tiers.length) {
							this.formData.tiers = [{
								min_cost: 0,
								percentage: 50
							}];
						}
					} else {
						this.formData.tiers = [];
					}

					if (this.formData.strategy_type === 'attribute_based') {
						if (!this.formData.rules.length) {
							this.formData.rules = [{
								attribute: '',
								value: '',
								percentage: 50
							}];
						}
					} else {
						this.formData.rules = [];
					}
				},

				addTier() {
					this.formData.tiers.push({
						min_cost: 0,
						percentage: 50
					});
				},

				addRule() {
					this.formData.rules.push({
						attribute: '',
						value: '',
						percentage: 50
					});
				},

				getCsrfToken() {
					const node = document.querySelector('meta[name="csrf-token"]');
					return node ? node.content : '';
				},

				async saveTemplate() {
					const url = this.formData.id ? `/admin/pricing-templates/${this.formData.id}` :
						'/admin/pricing-templates';
					const method = this.formData.id ? 'PUT' : 'POST';

					this.isSaving = true;
					try {
						const response = await fetch(url, {
							method,
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': this.getCsrfToken()
							},
							body: JSON.stringify(this.formData)
						});

						const data = await response.json();
						if (data.success) {
							this.toast(data.message || 'Template saved', 'success');
							setTimeout(() => window.location.reload(), 900);
						} else {
							this.toast(data.message || 'Unable to save template', 'error');
						}
					} catch (error) {
						console.error(error);
						this.toast('Unable to save template', 'error');
					} finally {
						this.isSaving = false;
					}
				},

				toast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.showToast = true;
					if (this.toastTimeout) {
						clearTimeout(this.toastTimeout);
					}
					this.toastTimeout = setTimeout(() => (this.showToast = false), 3000);
				}
			};
		}
	</script>
@endpush
