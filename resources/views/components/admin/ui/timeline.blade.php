{{-- Timeline Component for entity lifecycle visualization
    Usage:
    <x-admin.ui.timeline :steps="[
        ['label' => 'Purchased', 'date' => '2025-01-15', 'description' => 'Batch #12 - 10 units @ ৳100', 'status' => 'completed', 'url' => route('admin.purchase-batches.show', 12)],
        ['label' => 'In Stock', 'date' => '2025-01-15', 'description' => '10 units available', 'status' => 'completed'],
        ['label' => 'Sold (5 units)', 'date' => '2025-01-20', 'description' => 'Order #45', 'status' => 'completed', 'url' => route('admin.orders.show', 45)],
        ['label' => 'Remaining', 'description' => '5 units in stock', 'status' => 'current'],
    ]" />

    step.status: completed | current | upcoming
--}}
@props([
    'steps' => [],
    'compact' => false,
])

<div {{ $attributes->merge(['class' => '']) }}>
	<ol class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3">
		@foreach ($steps as $index => $step)
			@php
				$status = $step['status'] ?? 'upcoming';
				$dotClasses = match ($status) {
				    'completed' => 'bg-success-500 border-success-200 dark:border-success-800',
				    'current' => 'bg-brand-500 border-brand-200 dark:border-brand-800 ring-4 ring-brand-100 dark:ring-brand-900',
				    'failed' => 'bg-error-500 border-error-200 dark:border-error-800',
				    default => 'bg-gray-300 border-gray-200 dark:bg-gray-600 dark:border-gray-700',
				};
				$lineActive = $status === 'completed';
			@endphp
			<li class="mb-{{ $compact ? '6' : '8' }} ml-6 last:mb-0">
				{{-- Dot --}}
				<span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 rounded-full {{ $dotClasses }}">
					@if ($status === 'completed')
						<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
						</svg>
					@elseif($status === 'current')
						<span class="w-2 h-2 bg-white rounded-full"></span>
					@elseif($status === 'failed')
						<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
					@endif
				</span>

				{{-- Content --}}
				<div class="flex flex-col {{ !$compact ? 'gap-1' : '' }}">
					<div class="flex items-center gap-2 flex-wrap">
						@if (!empty($step['url']))
							<a href="{{ $step['url'] }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:underline">
								{{ $step['label'] }}
							</a>
						@else
							<span
								class="text-sm font-semibold text-gray-800 dark:text-white/90 {{ $status === 'upcoming' ? 'text-gray-400 dark:text-gray-500' : '' }}">
								{{ $step['label'] }}
							</span>
						@endif

						@if (!empty($step['badge']))
							<span
								class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ ($step['badgeColor'] ?? 'gray') === 'green' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : '' }}
                                {{ ($step['badgeColor'] ?? 'gray') === 'red' ? 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' : '' }}
                                {{ ($step['badgeColor'] ?? 'gray') === 'blue' ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' : '' }}
                                {{ ($step['badgeColor'] ?? 'gray') === 'orange' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400' : '' }}
                                {{ ($step['badgeColor'] ?? 'gray') === 'gray' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                            ">{{ $step['badge'] }}</span>
						@endif

						@if (!empty($step['date']))
							<time class="text-xs text-gray-400 dark:text-gray-500">{{ $step['date'] }}</time>
						@endif
					</div>

					@if (!empty($step['description']))
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ $step['description'] }}</p>
					@endif

					@if (!empty($step['meta']))
						<div class="flex items-center gap-3 mt-1">
							@foreach ($step['meta'] as $metaItem)
								<span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
									@if (!empty($metaItem['icon']))
										{!! $metaItem['icon'] !!}
									@endif
									{{ $metaItem['value'] }}
								</span>
							@endforeach
						</div>
					@endif
				</div>
			</li>
		@endforeach
	</ol>
</div>
