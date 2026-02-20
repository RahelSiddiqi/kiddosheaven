@props(['title', 'subtitle' => null, 'viewAllUrl' => null, 'viewAllText' => 'View All'])

<div class="flex items-end justify-between mb-6 md:mb-8">
    <div>
        <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $title }}</h2>
        @if ($subtitle)
            <p class="text-gray-500 text-sm mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($viewAllUrl)
        <a href="{{ $viewAllUrl }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary-dark transition group">
            {{ $viewAllText }}
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @endif
</div>
