@if ($paginator->hasPages())
    <nav class="kh-pagination" role="navigation" aria-label="Pagination Navigation">
        <ul class="kh-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="kh-pagination-item kh-pagination-item-disabled" aria-disabled="true">
                    <span class="kh-pagination-link kh-pagination-link-disabled">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:6px;">
                            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @lang('pagination.previous')
                    </span>
                </li>
            @else
                <li class="kh-pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="kh-pagination-link" rel="prev">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:6px;">
                            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @lang('pagination.previous')
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="kh-pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="kh-pagination-link" rel="next">
                        @lang('pagination.next')
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left:6px;">
                            <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>
            @else
                <li class="kh-pagination-item kh-pagination-item-disabled" aria-disabled="true">
                    <span class="kh-pagination-link kh-pagination-link-disabled">
                        @lang('pagination.next')
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left:6px;">
                            <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
