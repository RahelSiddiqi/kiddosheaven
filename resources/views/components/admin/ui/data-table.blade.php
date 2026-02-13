{{-- Data Table Component - Reusable sortable table with linked rows
    Usage:
    <x-admin.ui.data-table
        :columns="['SKU', 'Name', 'Stock', 'Price', 'Status']"
        :rows="$variants"
        :rowUrl="fn($row) => route('admin.products.variants.show', [$product, $row])"
        empty="No variants found"
    >
        <x-slot:row let:row>
            <td>{{ $row->sku }}</td>
            <td>{{ $row->name }}</td>
            ...
        </x-slot:row>
    </x-admin.ui.data-table>

    Or simple usage with $slot for full control:
    <x-admin.ui.data-table :columns="[...]" empty="No data">
        @foreach ($items as $item)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <td>...</td>
            </tr>
        @endforeach
    </x-admin.ui.data-table>
--}}
@props([
    'columns' => [],
    'columnAligns' => [], // ['left', 'left', 'right', 'right', 'center']
    'empty' => 'No data found.',
    'hasData' => true,
    'striped' => false,
    'compact' => false,
    'hoverable' => true,
    'title' => null,
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
])

<div
	{{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03] overflow-hidden']) }}
	@if ($searchable) x-data="{ search: '' }" @endif>

	{{-- Header with title and optional search --}}
	@if ($title || $searchable || isset($actions))
		<div class="flex items-center justify-between gap-4 px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
			@if ($title)
				<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h4>
			@else
				<div></div>
			@endif

			<div class="flex items-center gap-3">
				@if ($searchable)
					<div class="relative">
						<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<input x-model="search" type="text" placeholder="{{ $searchPlaceholder }}"
							class="pl-9 pr-4 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-transparent text-gray-800 dark:text-white/90 placeholder-gray-400 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 w-48">
					</div>
				@endif
				@if (isset($actions))
					{{ $actions }}
				@endif
			</div>
		</div>
	@endif

	{{-- Table --}}
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			@if (count($columns) > 0)
				<thead>
					<tr class="border-b border-gray-200 dark:border-gray-800">
						@foreach ($columns as $i => $column)
							@php
								$align = $columnAligns[$i] ?? 'left';
								$alignClass = match ($align) {
								    'right' => 'text-right',
								    'center' => 'text-center',
								    default => 'text-left',
								};
							@endphp
							<th
								class="{{ $alignClass }} px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap {{ $compact ? 'py-2' : 'py-3' }}">
								{{ $column }}
							</th>
						@endforeach
					</tr>
				</thead>
			@endif
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
				@if ($hasData)
					{{ $slot }}
				@endif
			</tbody>
		</table>
	</div>

	{{-- Empty state --}}
	@if (!$hasData)
		<div class="flex flex-col items-center justify-center py-12 px-6">
			<svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
				viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
					d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
			</svg>
			<p class="text-sm text-gray-400 dark:text-gray-500">{{ $empty }}</p>
		</div>
	@endif

	{{-- Footer (pagination, etc.) --}}
	@if (isset($footer))
		<div class="px-5 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30">
			{{ $footer }}
		</div>
	@endif
</div>
