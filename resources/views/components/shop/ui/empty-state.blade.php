@props(['icon' => '🔍', 'title', 'message' => '', 'actionUrl' => null, 'actionLabel' => null])

<div class="bg-white rounded-2xl border border-gray-100 p-10 md:p-16 text-center">
    <span class="text-5xl md:text-6xl block mb-4">{{ $icon }}</span>
    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">{{ $title }}</h3>
    @if ($message)
        <p class="text-gray-500 mb-8 max-w-md mx-auto">{{ $message }}</p>
    @endif
    @if ($actionUrl)
        <a href="{{ $actionUrl }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">
            {{ $actionLabel ?? 'Go Back' }}
        </a>
    @endif
    {{ $slot ?? '' }}
</div>
