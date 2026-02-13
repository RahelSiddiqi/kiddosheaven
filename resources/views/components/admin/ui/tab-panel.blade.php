{{-- Tab Panel Component (Alpine.js powered)
    Usage:
    <x-admin.ui.tab-panel :tabs="['Overview', 'Variants', 'Stock History', 'Orders']" default="Overview">
        <x-slot:tab_overview>
            ... overview content ...
        </x-slot:tab_overview>
        <x-slot:tab_variants>
            ... variants content ...
        </x-slot:tab_variants>
        <x-slot:tab_stock_history>
            ... stock history content ...
        </x-slot:tab_stock_history>
        <x-slot:tab_orders>
            ... orders content ...
        </x-slot:tab_orders>
    </x-admin.ui.tab-panel>

    Tab names will be converted to slot names: "Stock History" → tab_stock_history
--}}
@props([
    'tabs' => [],
    'default' => null,
    'counts' => [], // associative: ['Variants' => 5, 'Orders' => 12]
    'icons' => [], // associative: ['Overview' => '<svg...>']
])

@php
	$defaultTab = $default ?? ($tabs[0] ?? '');
	$toSlotName = fn($tab) => 'tab_' . str_replace([' ', '-'], '_', strtolower($tab));
@endphp

<div x-data="{ activeTab: '{{ $defaultTab }}' }" {{ $attributes->merge(['class' => '']) }}>
	{{-- Tab Navigation --}}
	<div class="border-b border-gray-200 dark:border-gray-800">
		<nav class="flex gap-1 -mb-px overflow-x-auto scrollbar-hide" role="tablist">
			@foreach ($tabs as $tab)
				<button @click="activeTab = '{{ $tab }}'"
					:class="activeTab === '{{ $tab }}'
					    ?
					    'border-brand-500 text-brand-600 dark:text-brand-400' :
					    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
					class="flex items-center gap-2 whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors"
					role="tab" :aria-selected="activeTab === '{{ $tab }}'">
					@if (!empty($icons[$tab]))
						<span class="w-4 h-4 flex-shrink-0">{!! $icons[$tab] !!}</span>
					@endif
					{{ $tab }}
					@if (isset($counts[$tab]))
						<span
							:class="activeTab === '{{ $tab }}'
							    ?
							    'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' :
							    'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
							class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full transition-colors">{{ $counts[$tab] }}</span>
					@endif
				</button>
			@endforeach
		</nav>
	</div>

	{{-- Tab Content --}}
	<div class="mt-6">
		@foreach ($tabs as $tab)
			@php $slotName = $toSlotName($tab); @endphp
			<div x-show="activeTab === '{{ $tab }}'" x-cloak role="tabpanel">
				@if (isset($$slotName))
					{{ $$slotName }}
				@endif
			</div>
		@endforeach
	</div>
</div>
