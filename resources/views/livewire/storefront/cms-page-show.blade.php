<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)] transition">Home</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">{{ $page->title }}</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($page->featured_image)
                <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $page->title }}" class="w-full h-48 sm:h-64 object-cover">
            @endif

            <div class="p-6 sm:p-10">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $page->title }}</h1>

                @if($page->updated_at)
                    <p class="text-xs text-gray-400 mb-6">Last updated {{ $page->updated_at->diffForHumans() }}</p>
                @endif

                <x-ui.separator />

                <div class="prose prose-sm sm:prose max-w-none mt-6 text-gray-700 leading-relaxed">
                    {!! $page->content !!}
                </div>
            </div>
        </div>

    </div>
</div>
