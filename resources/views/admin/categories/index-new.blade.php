@extends('admin.layouts.app')

@section('title', 'Categories — Kiddo\'s Heaven')

@section('content')
	<div x-data="categoryManager()" @toast.window="showToast($event.detail)">
		<!-- Toast Notification -->
		<div x-show="toastShow" x-transition.opacity.duration.300ms x-cloak
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
			:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'">
			<svg x-show="toastType === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<svg x-show="toastType === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
			</svg>
			<span x-text="toastMessage" class="text-sm font-medium"></span>
		</div>

		<!-- Stats Cards -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
			<x-admin.ui.stat-card title="Total Categories" :value="$stats['total'] ?? 0" icon="tag" color="brand" />
			<x-admin.ui.stat-card title="Active" :value="$stats['active'] ?? 0" icon="cart" color="green" />
			<x-admin.ui.stat-card title="With Products" :value="$stats['with_products'] ?? 0" icon="box" color="blue" />
			<x-admin.ui.stat-card title="Empty" :value="$stats['empty'] ?? 0" icon="stock" color="yellow" />
		</div>

		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<!-- Header -->
			<div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Categories</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organize your products into categories</p>
				</div>
				<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
					<div class="relative">
						<button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
							<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
									fill="" />
							</svg>
						</button>
						<input type="text" x-model="searchQuery" @input="filterCategories()" placeholder="Search categories..."
							class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-75" />
					</div>
					<div class="flex items-center gap-2">
						{{-- View Toggle: List / Tree --}}
						<button @click="viewMode = 'list'"
							:class="viewMode === 'list' ? 'bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' :
							    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
							class="h-10.5 px-3 rounded-lg transition-colors" title="List view">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
						<button @click="viewMode = 'tree'"
							:class="viewMode === 'tree' ? 'bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' :
							    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
							class="h-10.5 px-3 rounded-lg transition-colors" title="Hierarchical view">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 4h4v4H3V4zM13 4h4v4h-4V4zM13 14h4v4h-4v-4zM3 14h4v4H3v-4z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6h2M7 16h2M12 6V16M12 6h1M12 16h1" />
							</svg>
						</button>

						{{-- Expand All / Collapse All --}}
						<button x-show="viewMode === 'tree'" @click="expandAll()" x-cloak
							class="h-10.5 px-3 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
							title="Expand all">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
							</svg>
						</button>
						<button x-show="viewMode === 'tree'" @click="collapseAll()" x-cloak
							class="h-10.5 px-3 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
							title="Collapse all">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 9h4.5M15 9V4.5M15 9l5.5-5.5M15 15h4.5m-4.5 0v4.5m0-4.5l5.5 5.5" />
							</svg>
						</button>

						<button @click="showAddModal()"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Category
						</button>
					</div>
				</div>
			</div>

			{{-- List View (flat table) --}}
			<div x-show="viewMode === 'list'" class="overflow-hidden">
				<div class="max-w-full overflow-x-auto">
					<table class="min-w-full">
						<thead>
							<tr class="border-gray-200 border-y dark:border-gray-700">
								<th scope="col"
									class="px-5 sm:px-6 py-3 font-medium text-gray-500 text-start text-theme-xs uppercase tracking-wider dark:text-gray-400">
									Category</th>
								<th scope="col"
									class="px-4 py-3 font-medium text-gray-500 text-start text-theme-xs uppercase tracking-wider dark:text-gray-400">
									Parent</th>
								<th scope="col"
									class="px-4 py-3 font-medium text-gray-500 text-center text-theme-xs uppercase tracking-wider dark:text-gray-400">
									Products</th>
								<th scope="col"
									class="px-4 py-3 font-medium text-gray-500 text-start text-theme-xs uppercase tracking-wider dark:text-gray-400">
									Attributes</th>
								<th scope="col"
									class="px-4 py-3 font-medium text-gray-500 text-center text-theme-xs uppercase tracking-wider dark:text-gray-400">
									Status</th>
								<th scope="col" class="px-5 sm:px-6 py-3 text-right"><span class="sr-only">Actions</span></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							<template x-for="category in filteredCategories" :key="category.id">
								<tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
									<td class="px-5 sm:px-6 py-4">
										<div class="flex items-center gap-3" :style="'padding-left: ' + (category.depth * 28) + 'px'">
											{{-- Tree connector for children --}}
											<template x-if="category.depth > 0">
												<div class="flex items-center text-gray-300 dark:text-gray-600 shrink-0">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-width="1.5" d="M8 4v8h8" />
													</svg>
												</div>
											</template>
											<div class="shrink-0 flex items-center justify-center text-lg ring-1"
												:class="category.depth === 0 ? 'w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 ring-brand-500/20' : (category
												    .depth === 1 ? 'w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 ring-blue-500/20 text-base' :
												    'w-7 h-7 rounded-md bg-purple-50 dark:bg-purple-500/10 ring-purple-500/20 text-sm')"
												x-text="category.icon || (category.depth === 0 ? '📁' : (category.depth === 1 ? '📂' : '📄'))"></div>
											<div class="min-w-0">
												<p class="text-sm truncate"
													:class="category.depth === 0 ? 'font-semibold text-gray-800 dark:text-white/90' : (category.depth === 1 ?
													    'font-medium text-gray-700 dark:text-gray-300' : 'text-gray-600 dark:text-gray-400')"
													x-text="category.name"></p>
												<p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs" x-text="category.description || '—'">
												</p>
											</div>
										</div>
									</td>
									<td class="px-4 py-4">
										<span x-show="category.parent_name"
											class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-md"
											x-text="category.parent_name"></span>
										<span x-show="!category.parent_name" class="text-xs text-gray-400 dark:text-gray-500">Root</span>
									</td>
									<td class="px-4 py-4 text-center">
										<span class="inline-flex items-center justify-center min-w-6 h-6 px-2 text-xs font-medium rounded-full"
											:class="(category.total_products ?? category.products_count ?? category.product_count ?? category.productCount ??
											    0) > 0 ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400' :
											    'bg-gray-50 text-gray-400 dark:bg-gray-800 dark:text-gray-500'"
											x-text="category.total_products ?? category.products_count ?? category.product_count ?? category.productCount ?? 0"></span>
									</td>
									<td class="px-4 py-4">
										<div class="flex flex-wrap gap-1 justify-start">
											<template x-for="attr in (category.attributes || []).slice(0, 6)" :key="attr.id">
												<span class="px-2 py-0.5 rounded-full text-[11px] font-medium border"
													:class="attr.use_for_variants ?
													    'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-500/10 dark:text-purple-200 dark:border-purple-600/50' :
													    'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'"
													x-text="attr.name"></span>
											</template>
											<span x-show="(category.attributes || []).length === 0" class="text-[11px] text-gray-400">No
												attributes</span>
											<span x-show="(category.attributes || []).length > 6" class="text-[11px] text-gray-500"
												x-text="'+' + ((category.attributes || []).length - 6) + ' more'"></span>
										</div>
									</td>
									<td class="px-4 py-4 text-center">
										<span x-show="category.is_active"
											class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
											Active
										</span>
										<span x-show="!category.is_active"
											class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
											Inactive
										</span>
									</td>
									<td class="px-5 sm:px-6 py-4">
										<div class="flex items-center gap-1 justify-end">
											<a :href="'/admin/categories/' + category.id"
												class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
												title="View">
												<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
												</svg>
											</a>
											<button @click="editCategory(category)"
												class="p-2 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 transition-colors"
												title="Edit">
												<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
												</svg>
											</button>
											<button @click="confirmDelete(category)"
												class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
												title="Delete">
												<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
												</svg>
											</button>
										</div>
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>

				{{-- Empty State --}}
				<div x-show="filteredCategories.length === 0" class="px-6 py-16 text-center">
					<div
						class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-3xl">
						📁</div>
					<p class="text-sm font-medium text-gray-800 dark:text-white/90">No categories found</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
						x-text="searchQuery ? 'Try a different search term' : 'Create your first category to get started'"></p>
				</div>
			</div>

			{{-- Hierarchical Tree View --}}
			<div x-show="viewMode === 'tree'" class="px-5 sm:px-6 pb-5">
				<div x-ref="treeRoots">
					<template x-for="root in treeCategories" :key="root.id">
						<div class="mb-3 last:mb-0" :data-id="root.id">
							{{-- Root Category --}}
							<div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
								{{-- Root Header --}}
								<div
									class="flex items-center justify-between px-4 py-3 bg-gray-50/80 dark:bg-gray-800/50 cursor-pointer group/root"
									@click="toggleExpand(root.id)">
									<div class="flex items-center gap-3">
										{{-- Drag Handle --}}
										<div
											class="root-drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 opacity-0 group-hover/root:opacity-100 transition-opacity"
											@click.stop>
											<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
												<circle cx="9" cy="5" r="1.5" />
												<circle cx="15" cy="5" r="1.5" />
												<circle cx="9" cy="12" r="1.5" />
												<circle cx="15" cy="12" r="1.5" />
												<circle cx="9" cy="19" r="1.5" />
												<circle cx="15" cy="19" r="1.5" />
											</svg>
										</div>
										{{-- Expand/Collapse Arrow --}}
										<button
											class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-transform duration-200"
											:class="expanded[root.id] ? 'rotate-90' : ''" x-show="root.children && root.children.length > 0">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
											</svg>
										</button>
										<div class="w-6 h-6" x-show="!root.children || root.children.length === 0"></div>

										<div
											class="w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-lg ring-1 ring-brand-500/20"
											x-text="root.icon || '📁'"></div>
										<div class="flex items-center gap-2">
											<div>
												<div class="flex items-center gap-2">
													<p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="root.name"></p>
													<span x-show="root.is_active"
														class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">Active</span>
													<span x-show="!root.is_active"
														class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
												</div>
												<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
													<span
														x-text="(root.total_products ?? root.products_count ?? root.product_count ?? root.productCount ?? 0) + ' products'"></span>
													<span x-show="root.children && root.children.length > 0"
														class="text-gray-300 dark:text-gray-600 mx-1">&middot;</span>
													<span x-show="root.children && root.children.length > 0"
														x-text="root.children.length + ' subcategories'"></span>
												</p>
											</div>
											<div class="flex flex-wrap gap-1 mt-1">
												<template x-for="attr in (root.attributes || []).slice(0, 3)" :key="attr.id">
													<span class="px-2 py-0.5 rounded-full text-[11px] font-medium border"
														:class="attr.use_for_variants ?
														    'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-500/10 dark:text-purple-200 dark:border-purple-600/50' :
														    'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'"
														x-text="attr.name"></span>
												</template>
												<span x-show="(root.attributes || []).length > 3" class="text-[11px] text-gray-500"
													x-text="'+' + ((root.attributes || []).length - 3) + ' more'"></span>
											</div>
										</div>
									</div>
									<div class="flex items-center gap-1" @click.stop>
										<button @click="showAddModal(root.id)"
											class="p-2 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:text-green-400 dark:hover:bg-green-900/20 transition-colors"
											title="Add subcategory">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
											</svg>
										</button>
										<a :href="'/admin/categories/' + root.id"
											class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
											title="View">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
											</svg>
										</a>
										<button @click="editCategory(root)"
											class="p-2 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 transition-colors"
											title="Edit">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
											</svg>
										</button>
										<button @click="confirmDelete(root)"
											class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
											title="Delete">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
											</svg>
										</button>
									</div>
								</div>

								{{-- Children (Level 1) --}}
								<div x-show="expanded[root.id] && root.children && root.children.length > 0" :data-parent-id="root.id"
									class="sortable-children">
									<template x-for="child in root.children" :key="child.id">
										<div :data-id="child.id">
											{{-- Level 1 Child --}}
											<div
												class="flex items-center justify-between px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors group/child">
												<div class="flex items-center gap-3 pl-6">
													{{-- Drag Handle --}}
													<div
														class="child-drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 opacity-0 group-hover/child:opacity-100 transition-opacity"
														@click.stop>
														<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
															<circle cx="9" cy="5" r="1.5" />
															<circle cx="15" cy="5" r="1.5" />
															<circle cx="9" cy="12" r="1.5" />
															<circle cx="15" cy="12" r="1.5" />
															<circle cx="9" cy="19" r="1.5" />
															<circle cx="15" cy="19" r="1.5" />
														</svg>
													</div>
													{{-- Expand/Collapse for L1 --}}
													<button
														class="w-5 h-5 flex items-center justify-center rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-transform duration-200"
														:class="expanded[child.id] ? 'rotate-90' : ''" x-show="child.children && child.children.length > 0"
														@click.stop="toggleExpand(child.id)">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
														</svg>
													</button>
													<div class="w-5 h-5" x-show="!child.children || child.children.length === 0"></div>

													{{-- Tree connector line --}}
													<div class="flex items-center text-gray-300 dark:text-gray-600">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-width="1.5" d="M8 4v8h8" />
														</svg>
													</div>

													<div
														class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-sm ring-1 ring-blue-500/20"
														x-text="child.icon || '📂'"></div>
													<div class="flex items-center gap-4">
														<div class="w-xl">
															<div class="flex items-center gap-2">
																<p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="child.name"></p>
																<span x-show="child.is_active"
																	class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">Active</span>
																<span x-show="!child.is_active"
																	class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
															</div>
															<p class="text-xs text-gray-400 dark:text-gray-500">
																<span
																	x-text="(child.total_products ?? child.products_count ?? child.product_count ?? child.productCount ?? 0) + ' products'"></span>
																<span x-show="child.children && child.children.length > 0"
																	class="text-gray-300 dark:text-gray-600 mx-1">&middot;</span>
																<span x-show="child.children && child.children.length > 0"
																	x-text="child.children.length + ' subcategories'"></span>
															</p>
														</div>
														<div class="flex flex-wrap gap-1 mt-1 ml-6">
															<template x-for="attr in (child.attributes || []).slice(0, 3)" :key="attr.id">
																<span class="px-2 py-0.5 rounded-full text-[11px] font-medium border"
																	:class="attr.use_for_variants ?
																	    'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-500/10 dark:text-purple-200 dark:border-purple-600/50' :
																	    'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'"
																	x-text="attr.name"></span>
															</template>
															<span x-show="(child.attributes || []).length > 3" class="text-[11px] text-gray-500"
																x-text="'+' + ((child.attributes || []).length - 3) + ' more'"></span>
														</div>
													</div>
												</div>
												<div class="flex items-center gap-1" @click.stop>
													<button @click="showAddModal(child.id)"
														class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:text-green-400 dark:hover:bg-green-900/20 transition-colors"
														title="Add subcategory">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
														</svg>
													</button>
													<a :href="'/admin/categories/' + child.id"
														class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
														title="View">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													<button @click="editCategory(child)"
														class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 transition-colors"
														title="Edit">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
														</svg>
													</button>
													<button @click="confirmDelete(child)"
														class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
														title="Delete">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
														</svg>
													</button>
												</div>
											</div>

											{{-- Level 2 Children --}}
											<div x-show="expanded[child.id] && child.children && child.children.length > 0" :data-parent-id="child.id"
												class="sortable-grandchildren">
												<template x-for="grandchild in child.children" :key="grandchild.id">
													<div :data-id="grandchild.id"
														class="flex items-center justify-between px-4 py-2 border-t border-gray-50 dark:border-gray-800/50 hover:bg-gray-50/30 dark:hover:bg-white/[0.01] transition-colors group/grand">
														<div class="flex items-center gap-3 pl-16">
															{{-- Drag Handle --}}
															<div
																class="grand-drag-handle cursor-grab active:cursor-grabbing text-gray-200 hover:text-gray-400 dark:text-gray-700 dark:hover:text-gray-500 opacity-0 group-hover/grand:opacity-100 transition-opacity"
																@click.stop>
																<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
																	<circle cx="9" cy="5" r="1.5" />
																	<circle cx="15" cy="5" r="1.5" />
																	<circle cx="9" cy="12" r="1.5" />
																	<circle cx="15" cy="12" r="1.5" />
																	<circle cx="9" cy="19" r="1.5" />
																	<circle cx="15" cy="19" r="1.5" />
																</svg>
															</div>
															{{-- Tree connector --}}
															<div class="flex items-center text-gray-200 dark:text-gray-700">
																<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-width="1.5" d="M8 4v8h8" />
																</svg>
															</div>

															<div
																class="w-6 h-6 rounded-md bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-xs ring-1 ring-purple-500/20"
																x-text="grandchild.icon || '📄'"></div>
															<div>
																<div class="flex items-center gap-2">
																	<p class="text-sm text-gray-600 dark:text-gray-400" x-text="grandchild.name"></p>
																	<span x-show="grandchild.is_active"
																		class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">Active</span>
																	<span x-show="!grandchild.is_active"
																		class="px-1.5 py-0.5 text-[10px] leading-tight font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
																</div>
																<p class="text-xs text-gray-400 dark:text-gray-500"
																	x-text="(grandchild.total_products ?? grandchild.products_count ?? grandchild.product_count ?? grandchild.productCount ?? 0) + ' products'">
																</p>
																<div class="flex flex-wrap gap-1 mt-1">
																	<template x-for="attr in (grandchild.attributes || []).slice(0, 3)" :key="attr.id">
																		<span class="px-2 py-0.5 rounded-full text-[11px] font-medium border"
																			:class="attr.use_for_variants ?
																			    'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-500/10 dark:text-purple-200 dark:border-purple-600/50' :
																			    'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'"
																			x-text="attr.name"></span>
																	</template>
																	<span x-show="(grandchild.attributes || []).length > 3" class="text-[11px] text-gray-500"
																		x-text="'+' + ((grandchild.attributes || []).length - 3) + ' more'"></span>
																</div>
															</div>
														</div>
														<div class="flex items-center gap-1" @click.stop>
															<a :href="'/admin/categories/' + grandchild.id"
																class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
																title="View">
																<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
																</svg>
															</a>
															<button @click="editCategory(grandchild)"
																class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 transition-colors"
																title="Edit">
																<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
																</svg>
															</button>
															<button @click="confirmDelete(grandchild)"
																class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
																title="Delete">
																<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																		d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
																</svg>
															</button>
														</div>
													</div>
												</template>
											</div>
										</div>
									</template>
								</div>
							</div>
						</div>
					</template>
				</div>

				{{-- Empty State --}}
				<div x-show="treeCategories.length === 0" class="py-16 text-center">
					<div
						class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-3xl">
						📁</div>
					<p class="text-sm font-medium text-gray-800 dark:text-white/90">No categories found</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
						x-text="searchQuery ? 'Try a different search term' : 'Create your first category to get started'"></p>
				</div>
			</div>
		</div>

		{{-- Add/Edit Modal --}}
		<div x-show="showModal" x-cloak class="fixed inset-0 z-99999 overflow-y-auto flex items-center justify-center p-4">
			{{-- Backdrop --}}
			<div class="absolute inset-0 bg-black/50 transition-opacity" @click="closeModal()"></div>
			{{-- Panel --}}
			<div @click.stop
				class="relative rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 bg-white shadow-2xl max-w-lg w-full p-6">
				<div class="flex items-center justify-between mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" x-text="modalTitle"></h3>
					<button @click="closeModal()"
						class="h-9.5 w-9.5 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-gray-700 transition-colors">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				<form @submit.prevent="saveCategory()" class="space-y-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Category Name <span class="text-red-500">*</span>
						</label>
						<input type="text" x-model="formData.name" required placeholder="e.g., Electronics, Clothing, Toys"
							class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Icon (Emoji)
						</label>
						<input type="text" x-model="formData.icon" placeholder="📱 🎮 👕 📚"
							class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Description
						</label>
						<textarea x-model="formData.description" rows="3" placeholder="Brief description of this category"
						 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"></textarea>
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Parent Category
						</label>
						<select x-model="formData.parent_id"
							class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">None (Top Level)</option>
							@foreach ($categories as $cat)
								<option value="{{ $cat->id }}">{{ $cat->name }}</option>
								@if ($cat->children)
									@foreach ($cat->children as $child)
										<option value="{{ $child->id }}">&nbsp;&nbsp;└─ {{ $child->name }}</option>
									@endforeach
								@endif
							@endforeach
						</select>
					</div>

					<div class="flex items-center gap-3">
						<input type="checkbox" x-model="formData.is_active" id="is_active"
							class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
						<label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
					</div>

					<div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
						<button type="submit" :disabled="saving"
							class="h-10 flex-1 inline-flex items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 transition-colors">
							<svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
								</circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
							</svg>
							<span x-text="saving ? 'Saving...' : (editingId ? 'Update' : 'Create')"></span>
						</button>
						<button type="button" @click="closeModal()"
							class="h-10 flex-1 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
							Cancel
						</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Delete Confirmation Modal --}}
		<div x-show="showDeleteModal" x-cloak
			class="fixed inset-0 z-99999 overflow-y-auto flex items-center justify-center p-4">
			{{-- Backdrop --}}
			<div class="absolute inset-0 bg-black/50 transition-opacity" @click="showDeleteModal = false"></div>
			{{-- Panel --}}
			<div @click.stop
				class="relative rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 bg-white shadow-2xl max-w-sm w-full p-6 text-center">
				<div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
					<svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
					</svg>
				</div>
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">Delete Category</h3>
				<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
					Are you sure you want to delete <strong class="text-gray-700 dark:text-gray-300"
						x-text="deletingCategory?.name"></strong>?
				</p>
				<p class="text-xs text-gray-400 dark:text-gray-500 mb-6">
					Products in this category will be unassigned. Subcategories will be moved to the parent.
				</p>
				<div class="flex items-center gap-3">
					<button @click="executeDelete()" :disabled="deleting"
						class="h-10 flex-1 inline-flex items-center justify-center rounded-lg bg-red-500 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600 disabled:opacity-50 transition-colors">
						<svg x-show="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
							</circle>
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
						</svg>
						<span x-text="deleting ? 'Deleting...' : 'Delete'"></span>
					</button>
					<button @click="showDeleteModal = false"
						class="h-10 flex-1 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
						Cancel
					</button>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function categoryManager() {
				return {
					categories: @json($categories),
					filteredCategories: [],
					treeCategories: @json($categories),
					viewMode: 'list',
					searchQuery: '',
					showModal: false,
					showDeleteModal: false,
					modalTitle: 'Add Category',
					editingId: null,
					deletingCategory: null,
					saving: false,
					deleting: false,
					expanded: {},

					// Toast
					toastShow: false,
					toastMessage: '',
					toastType: 'success',
					toastTimer: null,

					formData: {
						name: '',
						description: '',
						icon: '',
						parent_id: '',
						is_active: true
					},

					sortableInstances: [],
					reorderSaving: false,

					init() {
						// Compute total products (own + all descendants) for each category
						this.computeTotalProducts(this.categories);
						this.filteredCategories = this.flattenCategories(this.categories);
						// Expand all root categories by default
						this.categories.forEach(cat => {
							this.expanded[cat.id] = true;
						});

						// Initialize sortable when tree view becomes visible
						this.$watch('viewMode', (val) => {
							if (val === 'tree') {
								this.$nextTick(() => setTimeout(() => this.initSortables(), 200));
							} else {
								this.destroySortables();
							}
						});

						// Surface flash messages from the server (redirect from edit/create)
						this.$nextTick(() => {
							@if (session('success'))
								this.toast(@json(session('success')), 'success');
							@elseif (session('error'))
								this.toast(@json(session('error')), 'error');
							@elseif ($errors->any())
								this.toast(@json(implode(' ', $errors->all())), 'error');
							@endif
						});
					},

					computeTotalProducts(cats) {
						cats.forEach(cat => {
							if (cat.children && cat.children.length) {
								this.computeTotalProducts(cat.children);
								cat.total_products = (cat.products_count || 0) + cat.children.reduce((sum, c) => sum + (c
									.total_products || 0), 0);
							} else {
								cat.total_products = cat.products_count || 0;
							}
						});
					},

					showToast(detail) {
						this.toastMessage = detail.message || '';
						this.toastType = detail.type || 'success';
						this.toastShow = true;
						if (this.toastTimer) clearTimeout(this.toastTimer);
						this.toastTimer = setTimeout(() => {
							this.toastShow = false;
						}, 3000);
					},

					toast(message, type = 'success') {
						this.showToast({
							message,
							type
						});
					},

					flattenCategories(cats, parentName = null, depth = 0, result = []) {
						cats.forEach(cat => {
							cat.parent_name = parentName;
							cat.depth = depth;
							result.push(cat);
							if (cat.children && cat.children.length) {
								this.flattenCategories(cat.children, cat.name, depth + 1, result);
							}
						});
						return result;
					},

					filterCategories() {
						const query = this.searchQuery.toLowerCase().trim();
						if (!query) {
							this.filteredCategories = this.flattenCategories(this.categories);
							this.treeCategories = this.categories;
							return;
						}

						// Flat list: filter all
						const all = this.flattenCategories(this.categories);
						this.filteredCategories = all.filter(cat =>
							cat.name.toLowerCase().includes(query) ||
							(cat.description && cat.description.toLowerCase().includes(query))
						);

						// Tree: filter and keep hierarchy
						this.treeCategories = this.filterTree(this.categories, query);
					},

					filterTree(cats, query) {
						let result = [];
						cats.forEach(cat => {
							const nameMatch = cat.name.toLowerCase().includes(query) ||
								(cat.description && cat.description.toLowerCase().includes(query));
							const filteredChildren = cat.children ? this.filterTree(cat.children, query) : [];

							if (nameMatch || filteredChildren.length > 0) {
								result.push({
									...cat,
									children: filteredChildren.length > 0 ? filteredChildren : (nameMatch ? cat
										.children : [])
								});
								// Auto-expand matched
								this.expanded[cat.id] = true;
							}
						});
						return result;
					},

					expandAll() {
						const setExpand = (cats) => {
							cats.forEach(cat => {
								this.expanded[cat.id] = true;
								if (cat.children && cat.children.length) setExpand(cat.children);
							});
						};
						setExpand(this.categories);
						this.$nextTick(() => setTimeout(() => this.initSortables(), 200));
					},

					collapseAll() {
						const ids = Object.keys(this.expanded);
						ids.forEach(id => {
							this.expanded[id] = false;
						});
					},

					destroySortables() {
						this.sortableInstances.forEach(s => {
							try {
								s.destroy();
							} catch (e) {}
						});
						this.sortableInstances = [];
					},

					initSortables() {
						this.destroySortables();
						if (typeof Sortable === 'undefined' || this.viewMode !== 'tree') return;

						const self = this;

						// Root categories sortable
						const treeRootsEl = this.$refs.treeRoots;
						if (treeRootsEl) {
							this.sortableInstances.push(Sortable.create(treeRootsEl, {
								handle: '.root-drag-handle',
								draggable: '> [data-id]',
								animation: 200,
								ghostClass: 'opacity-30',
								onEnd() {
									const ids = Array.from(treeRootsEl.querySelectorAll(':scope > [data-id]'))
										.map(el => parseInt(el.dataset.id));
									self.persistOrder(ids, null);
								}
							}));
						}

						// Level 1 children sortable
						this.$el.querySelectorAll('.sortable-children').forEach(container => {
							const pid = parseInt(container.dataset.parentId);
							if (!pid) return;
							self.sortableInstances.push(Sortable.create(container, {
								handle: '.child-drag-handle',
								draggable: '> [data-id]',
								animation: 200,
								ghostClass: 'opacity-30',
								onEnd() {
									const ids = Array.from(container.querySelectorAll(':scope > [data-id]'))
										.map(el => parseInt(el.dataset.id));
									self.persistOrder(ids, pid);
								}
							}));
						});

						// Level 2 grandchildren sortable
						this.$el.querySelectorAll('.sortable-grandchildren').forEach(container => {
							const pid = parseInt(container.dataset.parentId);
							if (!pid) return;
							self.sortableInstances.push(Sortable.create(container, {
								handle: '.grand-drag-handle',
								draggable: '> [data-id]',
								animation: 200,
								ghostClass: 'opacity-30',
								onEnd() {
									const ids = Array.from(container.querySelectorAll(':scope > [data-id]'))
										.map(el => parseInt(el.dataset.id));
									self.persistOrder(ids, pid);
								}
							}));
						});
					},

					async persistOrder(order, parentId) {
						if (this.reorderSaving) return;
						this.reorderSaving = true;

						try {
							const response = await fetch('/admin/categories/reorder', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								},
								body: JSON.stringify({
									order: order,
									parent_id: parentId
								})
							});

							if (response.ok) {
								this.toast('Order saved', 'success');
								setTimeout(() => window.location.reload(), 700);
							} else {
								this.toast('Failed to save order', 'error');
							}
						} catch (e) {
							console.error(e);
							this.toast('Failed to save order', 'error');
						} finally {
							this.reorderSaving = false;
						}
					},

					toggleExpand(id) {
						this.expanded[id] = !this.expanded[id];
						if (this.expanded[id] && this.viewMode === 'tree') {
							this.$nextTick(() => setTimeout(() => this.initSortables(), 200));
						}
					},

					showAddModal(parentId = '') {
						this.modalTitle = 'Add Category';
						this.editingId = null;
						this.formData = {
							name: '',
							description: '',
							icon: '',
							parent_id: parentId ? String(parentId) : '',
							is_active: true
						};
						this.showModal = true;
					},

					editCategory(category) {
						this.modalTitle = 'Edit Category';
						this.editingId = category.id;
						this.formData = {
							name: category.name,
							description: category.description || '',
							icon: category.icon || '',
							parent_id: category.parent_id ? String(category.parent_id) : '',
							is_active: category.is_active
						};
						this.showModal = true;
					},

					closeModal() {
						this.showModal = false;
						this.saving = false;
					},

					confirmDelete(category) {
						this.deletingCategory = category;
						this.showDeleteModal = true;
					},

					async saveCategory() {
						this.saving = true;
						const url = this.editingId ?
							'/admin/categories/' + this.editingId :
							'/admin/categories';

						const method = this.editingId ? 'PUT' : 'POST';

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

							if (response.ok) {
								this.toast(this.editingId ? 'Category updated successfully' : 'Category created successfully');
								this.closeModal();
								setTimeout(() => window.location.reload(), 800);
							} else {
								const errors = data.errors ? Object.values(data.errors).flat().join(', ') :
									'Failed to save category';
								this.toast(errors, 'error');
								this.saving = false;
							}
						} catch (error) {
							console.error('Error:', error);
							this.toast('An error occurred. Please try again.', 'error');
							this.saving = false;
						}
					},

					async executeDelete() {
						if (!this.deletingCategory) return;
						this.deleting = true;

						try {
							const response = await fetch('/admin/categories/' + this.deletingCategory.id, {
								method: 'DELETE',
								headers: {
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
								}
							});

							if (response.ok) {
								this.toast('Category deleted successfully');
								this.showDeleteModal = false;
								this.deleting = false;
								setTimeout(() => window.location.reload(), 800);
							} else {
								this.toast('Failed to delete category', 'error');
								this.deleting = false;
							}
						} catch (error) {
							console.error('Error:', error);
							this.toast('An error occurred. Please try again.', 'error');
							this.deleting = false;
						}
					}
				}
			}
		</script>
	@endpush
@endsection
