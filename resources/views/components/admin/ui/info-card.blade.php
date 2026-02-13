{{-- Info Card Component - Key/Value display for entity properties
    Usage:
    <x-admin.ui.info-card title="Product Details" :items="[
        ['label' => 'SKU', 'value' => 'KH-001'],
        ['label' => 'Category', 'value' => 'Toys', 'url' => route('admin.categories.show', 1)],
        ['label' => 'Status', 'value' => 'Active', 'badge' => 'green'],
        ['label' => 'Stock', 'value' => '25 units', 'badge' => 'blue'],
        ['label' => 'Cost (FIFO)', 'value' => '৳1,050.00', 'mono' => true],
    ]" />
--}}
@props([
    'title' => null,
    'items' => [],
    'columns' => 1, // 1 or 2 column layout
    'compact' => false,
    'icon' => null,
])

<div
	{{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]']) }}>
	@if ($title)
		<div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
			@if ($icon)
				<span class="text-gray-400 dark:text-gray-500">{!! $icon !!}</span>
			@endif
			<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h4>
		</div>
	@endif

	<div class="{{ $columns === 2 ? 'grid grid-cols-1 sm:grid-cols-2' : '' }}">
		@foreach ($items as $index => $item)
			@php
				$isLast = $loop->last || ($columns === 2 && $loop->remaining < 2);
				$borderClass = !$isLast ? 'border-b border-gray-100 dark:border-gray-800/50' : '';
			@endphp
			<div
				class="flex items-center justify-between gap-4 px-5 {{ $compact ? 'py-2.5' : 'py-3' }} {{ $borderClass }}
                {{ $columns === 2 && $loop->index % 2 === 0 ? 'sm:border-r sm:border-gray-100 sm:dark:border-gray-800/50' : '' }}">
				<dt class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $item['label'] }}</dt>
				<dd
					class="text-sm text-right {{ !empty($item['mono']) ? 'font-mono' : 'font-medium' }} text-gray-800 dark:text-white/90 truncate">
					@if (!empty($item['badge']))
						@php
							$badgeColor = $item['badge'];
							$badgeClasses = match ($badgeColor) {
							    'green' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
							    'red' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
							    'blue' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
							    'orange' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
							    'purple' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
							    default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
							};
						@endphp
						<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
							{{ $item['value'] }}
						</span>
					@elseif(!empty($item['url']))
						<a href="{{ $item['url'] }}" class="text-brand-600 dark:text-brand-400 hover:underline">{{ $item['value'] }}</a>
					@else
						{{ $item['value'] }}
					@endif
				</dd>
			</div>
		@endforeach
	</div>

	@if (isset($footer))
		<div class="px-5 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30 rounded-b-xl">
			{{ $footer }}
		</div>
	@endif
</div>
