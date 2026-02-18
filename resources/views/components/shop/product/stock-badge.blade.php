@props(['quantity' => 0, 'compact' => false, 'lowThreshold' => 5])

@if ($compact)
    @if ($quantity <= 0)
        <span class="text-[10px] text-red-600 font-medium">Out of stock</span>
    @elseif ($quantity <= $lowThreshold)
        <span class="text-[10px] text-amber-600 font-medium">Only {{ $quantity }} left</span>
    @endif
@else
    @if ($quantity <= 0)
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            <span class="text-sm text-red-600 font-semibold">Out of Stock</span>
        </div>
    @elseif ($quantity <= $lowThreshold)
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-sm text-amber-600 font-semibold">Only {{ $quantity }} left in stock</span>
        </div>
    @else
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            <span class="text-sm text-green-600 font-semibold">In Stock</span>
        </div>
    @endif
@endif
