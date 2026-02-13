{{-- Stat Card Component for Dashboards & Entity Pages
    Usage: <x-admin.ui.stat-card title="Revenue" :value="$revenue" icon="currency" trend="+12%" color="green" :url="route('...')" />
--}}
@props([
    'title' => null,
    'label' => null,
    'value',
    'subtitle' => null,
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'brand',
    'url' => null,
    'compact' => false,
])

@php
	// Support both 'title' and 'label' props (label is a legacy alias)
	$title = $title ?? ($label ?? '');
	// Normalize icon aliases
	$iconMap = ['package' => 'box', 'dollar' => 'currency', 'trending-down' => 'x-circle', 'lightning' => 'flash'];
	$icon = $iconMap[$icon] ?? $icon;
@endphp

@php
	$colorMap = [
	    'brand' => [
	        'bg' => 'bg-brand-50 dark:bg-brand-500/10',
	        'icon' => 'text-brand-500 dark:text-brand-400',
	        'ring' => 'ring-brand-500/20',
	    ],
	    'green' => [
	        'bg' => 'bg-success-50 dark:bg-success-500/10',
	        'icon' => 'text-success-500 dark:text-success-400',
	        'ring' => 'ring-success-500/20',
	    ],
	    'red' => [
	        'bg' => 'bg-error-50 dark:bg-error-500/10',
	        'icon' => 'text-error-500 dark:text-error-400',
	        'ring' => 'ring-error-500/20',
	    ],
	    'yellow' => [
	        'bg' => 'bg-warning-50 dark:bg-warning-500/10',
	        'icon' => 'text-warning-500 dark:text-warning-400',
	        'ring' => 'ring-warning-500/20',
	    ],
	    'blue' => [
	        'bg' => 'bg-blue-light-50 dark:bg-blue-light-500/10',
	        'icon' => 'text-blue-light-500 dark:text-blue-light-400',
	        'ring' => 'ring-blue-light-500/20',
	    ],
	    'purple' => [
	        'bg' => 'bg-theme-purple-500/10',
	        'icon' => 'text-theme-purple-500',
	        'ring' => 'ring-theme-purple-500/20',
	    ],
	    'orange' => [
	        'bg' => 'bg-orange-50 dark:bg-orange-500/10',
	        'icon' => 'text-orange-500 dark:text-orange-400',
	        'ring' => 'ring-orange-500/20',
	    ],
	];
	$c = $colorMap[$color] ?? $colorMap['brand'];
@endphp

<{{ $url ? 'a href="' . $url . '"' : 'div' }}
	class="group rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] {{ $compact ? 'p-4' : 'p-5' }} transition-all {{ $url ? 'hover:border-brand-200 hover:shadow-theme-md dark:hover:border-brand-800 cursor-pointer' : '' }}">

	<div class="flex items-center justify-between">
		<div class="flex-1 min-w-0">
			<p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $title }}</p>
			<p class="{{ $compact ? 'text-xl' : 'text-2xl' }} font-bold text-gray-800 dark:text-white/90 mt-1">{{ $value }}
			</p>
			@if ($subtitle)
				<p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $subtitle }}</p>
			@endif
			@if ($trend)
				<div class="flex items-center gap-1 mt-2">
					<svg class="w-4 h-4 {{ $trendUp ? 'text-success-500' : 'text-error-500 rotate-180' }}" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
					</svg>
					<span
						class="text-xs font-medium {{ $trendUp ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">{{ $trend }}</span>
				</div>
			@endif
		</div>

		@if ($icon)
			<div class="flex-shrink-0 ml-4">
				<div
					class="flex items-center justify-center {{ $compact ? 'w-10 h-10' : 'w-12 h-12' }} rounded-xl {{ $c['bg'] }} {{ $c['icon'] }} ring-1 {{ $c['ring'] }}">
					@if ($icon === 'currency')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					@elseif($icon === 'box')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
					@elseif($icon === 'cart')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
						</svg>
					@elseif($icon === 'trending')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
						</svg>
					@elseif($icon === 'users')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
						</svg>
					@elseif($icon === 'stock')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
						</svg>
					@elseif($icon === 'profit')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
						</svg>
					@elseif($icon === 'alert')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
						</svg>
					@elseif($icon === 'tag')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
						</svg>
					@elseif($icon === 'star')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
						</svg>
					@elseif($icon === 'flash')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
						</svg>
					@elseif($icon === 'clock')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					@elseif($icon === 'check')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					@elseif($icon === 'x-circle')
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					@else
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
						</svg>
					@endif
				</div>
			</div>
		@endif
	</div>

	@if ($url)
		<div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
			<span
				class="text-xs font-medium text-brand-500 group-hover:text-brand-600 dark:text-brand-400 flex items-center gap-1">
				View Details
				<svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor"
					viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg>
			</span>
		</div>
	@endif

	</{{ $url ? 'a' : 'div' }}>
