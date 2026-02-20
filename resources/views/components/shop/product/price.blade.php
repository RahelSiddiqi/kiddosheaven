@props(['product', 'size' => 'md'])

@php
    $price        = $product->price;
    $discount     = $product->discount_price;
    $isPercentage = ($product->discount_type ?? 'percentage') === 'percentage';
    $hasDiscount  = $discount && $discount > 0
                    && ($isPercentage ? $discount < 100 : $discount < $price);

    if ($hasDiscount) {
        if ($isPercentage) {
            $discountPct   = round($discount);
            $originalPrice = round($price / (1 - $discount / 100));
            $displayPrice  = $price; // price IS already the selling price
        } else {
            $originalPrice = $price + $discount;
            $discountPct   = round(($discount / $originalPrice) * 100);
            $displayPrice  = $price;
        }
    } else {
        $discountPct   = 0;
        $originalPrice = null;
        $displayPrice  = $price;
    }

    $sizeClasses = match($size) {
        'sm'    => ['price' => 'text-sm font-bold', 'original' => 'text-xs'],
        'md'    => ['price' => 'text-base md:text-lg font-bold', 'original' => 'text-xs'],
        'lg'    => ['price' => 'text-2xl md:text-3xl font-bold', 'original' => 'text-base md:text-lg'],
        'xl'    => ['price' => 'text-3xl md:text-4xl font-extrabold', 'original' => 'text-lg md:text-xl'],
        default => ['price' => 'text-base font-bold', 'original' => 'text-xs'],
    };
@endphp

<div class="flex items-baseline gap-2 flex-wrap">
    <span class="{{ $sizeClasses['price'] }} text-primary-dark">৳{{ number_format($displayPrice, 0) }}</span>
    @if ($hasDiscount)
        <span class="{{ $sizeClasses['original'] }} text-gray-400 line-through">৳{{ number_format($originalPrice, 0) }}</span>
        <span class="px-1.5 py-0.5 rounded bg-red-50 text-red-600 text-[10px] font-semibold">-{{ $discountPct }}%</span>
    @endif
</div>
