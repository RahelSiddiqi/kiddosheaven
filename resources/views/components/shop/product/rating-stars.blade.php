@props(['rating' => 0, 'count' => 0, 'compact' => false, 'size' => 'sm'])

@php
    $rating = floatval($rating);
    $fullStars = floor($rating);
    $hasHalf = ($rating - $fullStars) >= 0.5;
    $sizeClasses = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        default => 'w-3.5 h-3.5',
    };
@endphp

@if ($count > 0 || !$compact)
    <div class="flex items-center gap-1">
        <div class="flex items-center">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $fullStars)
                    <svg class="{{ $sizeClasses }} text-amber-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @elseif ($i == $fullStars + 1 && $hasHalf)
                    <svg class="{{ $sizeClasses }} text-amber-400" viewBox="0 0 20 20">
                        <defs><linearGradient id="half-{{ $i }}-{{ rand() }}"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db"/></linearGradient></defs>
                        <path fill="url(#half-{{ $i }}-{{ rand() }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @else
                    <svg class="{{ $sizeClasses }} text-gray-300 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endif
            @endfor
        </div>
        @if (!$compact && $count > 0)
            <span class="text-xs text-gray-500">({{ number_format($rating, 1) }} &middot; {{ $count }})</span>
        @elseif ($compact && $count > 0)
            <span class="text-xs text-gray-500">{{ number_format($rating, 1) }}</span>
        @endif
    </div>
@endif
