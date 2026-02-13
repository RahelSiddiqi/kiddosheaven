{{-- Empty State Component
    Usage:
    <x-admin.ui.empty-state
        title="No products found"
        description="Get started by creating your first product."
        icon="box"
        actionUrl="{{ route('admin.products.create') }}"
        actionLabel="Create Product"
    />
--}}
@props([
    'title' => 'No data found',
    'description' => null,
    'icon' => 'box', // box | cart | users | search | document | chart
    'actionUrl' => null,
    'actionLabel' => 'Get Started',
])

@php
	$icons = [
	    'box' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
	    'cart' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>',
	    'users' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
	    'search' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
	    'document' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
	    'chart' =>
	        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
	];
	$svgPath = $icons[$icon] ?? $icons['box'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6']) }}>
	<div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
		<svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
			viewBox="0 0 24 24">{!! $svgPath !!}</svg>
	</div>

	<h3 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-1">{{ $title }}</h3>

	@if ($description)
		<p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">{{ $description }}</p>
	@endif

	@if ($actionUrl)
		<a href="{{ $actionUrl }}"
			class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
			</svg>
			{{ $actionLabel }}
		</a>
	@endif

	@if (isset($slot) && $slot->isNotEmpty())
		<div class="mt-4">{{ $slot }}</div>
	@endif
</div>
