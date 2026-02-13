<div class="overflow-x-auto">
	<table class="w-full text-left border-collapse">
		<thead>
			<tr class="border-b border-gray-200 dark:border-gray-700">
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10"></th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Attribute</th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Values</th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Options</th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</th>
				<th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">
					Actions</th>
			</tr>
		</thead>
		<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="sortable-attributes">
			@forelse ($attributes as $attribute)
				<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $attribute->id }}">
					<td class="px-6 py-4">
						<svg class="w-5 h-5 text-gray-400 drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
						</svg>
					</td>
					<td class="px-6 py-4">
						<div class="flex flex-col">
							<span class="font-medium text-gray-800 dark:text-white/90">{{ $attribute->name }}</span>
							@if ($attribute->description)
								<span
									class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($attribute->description, 50) }}</span>
							@endif
						</div>
					</td>
					<td class="px-6 py-4">
						@php
							$typeColors = [
							    'text' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
							    'select' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
							    'multiselect' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
							    'boolean' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
							    'number' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
							    'date' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
							];
							$typeDescriptions = [
							    'text' => 'Text input',
							    'select' => 'Dropdown selection',
							    'multiselect' => 'Multiple selection',
							    'boolean' => 'Yes/No toggle',
							    'number' => 'Numeric input',
							    'date' => 'Date picker',
							];
						@endphp
						<div class="flex flex-col items-start">
							<span
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$attribute->type] ?? 'bg-gray-100 text-gray-700' }}">
								{{ ucfirst($attribute->type) }}
							</span>
							<span class="text-xs text-gray-400 mt-1">{{ $typeDescriptions[$attribute->type] ?? '' }}</span>
						</div>
					</td>
					<td class="px-6 py-4">
						@if (in_array($attribute->type, ['select', 'multiselect']))
							@if ($attribute->values && $attribute->values->count() > 0)
								<div class="flex flex-wrap gap-1">
									@foreach ($attribute->values->take(3) as $value)
										<span
											class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
											{{ $value->value }}
										</span>
									@endforeach
									@if ($attribute->values->count() > 3)
										<span
											class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
											+{{ $attribute->values->count() - 3 }} more
										</span>
									@endif
								</div>
							@else
								<span class="text-xs text-gray-400 italic">No values defined</span>
							@endif
						@else
							<span class="text-xs text-gray-400">Entered per product</span>
						@endif
					</td>
					<td class="px-6 py-4">
						<div class="flex flex-wrap gap-1">
							@if ($attribute->is_required)
								<span
									class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
									Required
								</span>
							@endif
							@if ($attribute->is_filterable)
								<span
									class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
									Filterable
								</span>
							@endif
						</div>
					</td>
					<td class="px-6 py-4">
						@php
							$categories = $attribute->categories ?? collect();
							$categoryCount = $categories->count();
						@endphp
						@if ($categoryCount > 0)
							<a href="{{ route('admin.categories.show', [$categories->first()->id]) }}"
								class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
								{{ $categoryCount }} category(ies)
							</a>
						@else
							<span class="text-sm text-gray-400">Not assigned</span>
						@endif
					</td>
					<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
						<div class="flex items-center gap-2 justify-end">
							<a href="{{ route('admin.attributes.values.edit', $attribute->id) }}"
								class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
								<x-icons.list />
							</a>
							<button type="button"
								onclick="openEditModal({{ $attribute->id }}, '{{ addslashes($attribute->name) }}', '{{ $attribute->type }}', {{ $attribute->is_required ? 1 : 0 }}, {{ $attribute->is_filterable ? 1 : 0 }}, '{{ addslashes($attribute->description ?? '') }}', '{{ addslashes($attribute->values->pluck('value')->implode('__NEWLINE__') ?? '') }}')"
								class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
								<x-icons.edit />
							</button>
							<button type="button" onclick="deleteAttribute({{ $attribute->id }})"
								class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-600">
								<x-icons.delete />
							</button>
						</div>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="7" class="px-6 py-12 text-center">
						<div class="flex flex-col items-center justify-center">
							<svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
							</svg>
							<p class="text-gray-500 dark:text-gray-400 mb-2">No attributes found</p>
							<p class="text-sm text-gray-400 dark:text-gray-500">Create your first attribute to get started</p>
						</div>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</div>
