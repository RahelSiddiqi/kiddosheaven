{{-- Universal Entity Header Component
    Usage: <x-admin.ui.entity-header :title="$title" :subtitle="$subtitle" :breadcrumbs="$breadcrumbs" :actions="$actions" :links="$links" />
--}}
@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'badgeColor' => 'brand',
    'breadcrumbs' => [],
    'actions' => [],
    'links' => [],
    'backUrl' => null,
    'backLabel' => 'Back',
])

<div class="mb-6 space-y-3">
	{{-- Breadcrumbs --}}
	@if (count($breadcrumbs) > 0)
		<nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
			<a href="{{ route('admin.dashboard') }}" class="hover:text-brand-500 transition-colors">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
				</svg>
			</a>
			@foreach ($breadcrumbs as $crumb)
				@if (isset($crumb['label']))
					<svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
					@if (isset($crumb['url']))
						<a href="{{ $crumb['url'] }}" class="hover:text-brand-500 transition-colors">{{ $crumb['label'] }}</a>
					@else
						<span class="text-gray-800 dark:text-white/90 font-medium">{{ $crumb['label'] }}</span>
					@endif
				@endif
			@endforeach
		</nav>
	@endif

	{{-- Main Header --}}
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<div class="flex items-center gap-3">
			@if ($backUrl)
				<a href="{{ $backUrl }}"
					class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors"
					title="{{ $backLabel }}">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
			@endif
			<div>
				<div class="flex items-center gap-2.5">
					<h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h1>
					@if ($badge)
						<span
							class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if ($badgeColor === 'green') bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400
                            @elseif($badgeColor === 'red') bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400
                            @elseif($badgeColor === 'yellow') bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400
                            @elseif($badgeColor === 'blue') bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/15 dark:text-blue-light-400
                            @else bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400 @endif">
							{{ $badge }}
						</span>
					@endif
				</div>
				@if ($subtitle)
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $subtitle }}</p>
				@endif
			</div>
		</div>

		{{-- Action Buttons --}}
		@if (
			(is_array($actions) && count($actions) > 0) ||
				(isset($actions) && $actions instanceof \Illuminate\View\ComponentSlot && $actions->isNotEmpty()) ||
				$slot->isNotEmpty())
			<div class="flex items-center gap-2 flex-wrap">
				@if (isset($actions) && $actions instanceof \Illuminate\View\ComponentSlot && $actions->isNotEmpty())
					{{ $actions }}
				@elseif(is_array($actions))
					@foreach ($actions as $action)
						@if (isset($action['url']))
							<a href="{{ $action['url'] }}"
								class="h-10 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors
                                @if (($action['style'] ?? 'secondary') === 'primary' || ($action['primary'] ?? false)) bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600
                                @elseif(($action['style'] ?? 'secondary') === 'danger')
                                    bg-error-500 text-white shadow-theme-xs hover:bg-error-600
                                @else
                                    border border-gray-300 bg-white text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 @endif">
								@if (isset($action['icon']))
									{!! $action['icon'] !!}
								@endif
								{{ $action['label'] }}
							</a>
						@else
							<button type="button" @if (isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
								class="h-10 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors
                                @if (($action['style'] ?? 'secondary') === 'primary' || ($action['primary'] ?? false)) bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600
                                @elseif(($action['style'] ?? 'secondary') === 'danger')
                                    bg-error-500 text-white shadow-theme-xs hover:bg-error-600
                                @else
                                    border border-gray-300 bg-white text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 @endif">
								@if (isset($action['icon']))
									{!! $action['icon'] !!}
								@endif
								{{ $action['label'] }}
							</button>
						@endif
					@endforeach
				@endif
				{{ $slot }}
			</div>
		@endif
	</div>

	{{-- Quick Navigation Links (Entity Links) --}}
	@if (count($links) > 0)
		<div class="flex items-center gap-2 flex-wrap pt-1">
			@foreach ($links as $link)
				<a href="{{ $link['url'] }}"
					class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all
                        {{ $link['active'] ?? false
																								    ? 'border-brand-200 bg-brand-50 text-brand-700 dark:border-brand-800 dark:bg-brand-500/10 dark:text-brand-400'
																								    : 'border-gray-200 bg-white text-gray-600 hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-brand-800 dark:hover:bg-brand-500/10 dark:hover:text-brand-400' }}">
					@if (isset($link['icon']))
						{!! $link['icon'] !!}
					@endif
					{{ $link['label'] }}
					@if (isset($link['count']))
						<span
							class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold
                            {{ $link['active'] ?? false
																												    ? 'bg-brand-200 text-brand-800 dark:bg-brand-500/30 dark:text-brand-300'
																												    : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
							{{ $link['count'] }}
						</span>
					@endif
				</a>
			@endforeach
		</div>
	@endif
</div>
