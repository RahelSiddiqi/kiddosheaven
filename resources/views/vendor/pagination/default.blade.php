@if ($paginator->hasPages())
	<nav class="flex justify-center mt-4" role="navigation" aria-label="Pagination Navigation">
		<ul class="inline-flex items-center gap-1">
			{{-- Previous Page Link --}}
			@if ($paginator->onFirstPage())
				<li aria-disabled="true" aria-label="@lang('pagination.previous')">
					<span class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200"
						aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
					</span>
				</li>
			@else
				<li>
					<a href="{{ $paginator->previousPageUrl() }}"
						class="inline-flex items-center justify-center w-8 h-8 rounded bg-white border border-gray-200 text-gray-700 hover:bg-[var(--color-primary-dark)] hover:text-white transition"
						rel="prev" aria-label="@lang('pagination.previous')">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
					</a>
				</li>
			@endif

			{{-- Pagination Elements --}}
			@foreach ($elements as $element)
				{{-- "Three Dots" Separator --}}
				@if (is_string($element))
					<li aria-disabled="true">
						<span
							class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 text-gray-400 cursor-not-allowed">{{ $element }}</span>
					</li>
				@endif

				{{-- Array Of Links --}}
				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<li aria-current="page">
								<span
									class="inline-flex items-center justify-center w-8 h-8 rounded bg-[var(--color-primary-dark)] text-white font-bold border-2 border-[--admin-accent] shadow">{{ $page }}</span>
							</li>
						@else
							<li>
								<a href="{{ $url }}"
									class="inline-flex items-center justify-center w-8 h-8 rounded bg-white border border-gray-200 text-gray-700 hover:bg-[var(--color-primary-dark)] hover:text-white transition">{{ $page }}</a>
							</li>
						@endif
					@endforeach
				@endif
			@endforeach

			{{-- Next Page Link --}}
			@if ($paginator->hasMorePages())
				<li>
					<a href="{{ $paginator->nextPageUrl() }}"
						class="inline-flex items-center justify-center w-8 h-8 rounded bg-white border border-gray-200 text-gray-700 hover:bg-[var(--color-primary-dark)] hover:text-white transition"
						rel="next" aria-label="@lang('pagination.next')">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				</li>
			@else
				<li aria-disabled="true" aria-label="@lang('pagination.next')">
					<span class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200"
						aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
				</li>
			@endif
		</ul>
	</nav>
@endif
