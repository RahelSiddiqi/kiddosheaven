@props(['item', 'itemKey'])

@php
    $img = $item['image'] ?? $item['image_path'] ?? null;
    $itemQty = $item['quantity'] ?? 1;
    $itemSubtotal = ($item['price'] ?? 0) * $itemQty;
    $productId = $item['product_id'] ?? $itemKey;
    $variantId = $item['variant_id'] ?? null;
    $variantAttrs = $item['variant_attributes'] ?? [];
@endphp

<div class="p-4 md:p-6">
    <div class="flex gap-4">
        {{-- Product Image --}}
        <a href="{{ route('products.show', $item['slug']) }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0">
            @if ($img)
                <img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center"><span class="text-3xl">🧸</span></div>
            @endif
        </a>

        {{-- Details --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <a href="{{ route('products.show', $item['slug']) }}" class="font-semibold text-sm sm:text-base text-gray-800 hover:text-primary transition line-clamp-2">{{ $item['name'] }}</a>

                    {{-- Variant Attributes --}}
                    @if (!empty($variantAttrs))
                        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1">
                            @foreach ($variantAttrs as $attrName => $attrValue)
                                <span class="inline-flex items-center text-xs text-gray-500">
                                    <span class="font-medium text-gray-600">{{ $attrName }}:</span>
                                    @if (in_array(strtolower($attrName), ['color', 'colour', 'রঙ']))
                                        <span class="w-3.5 h-3.5 rounded-full border border-gray-200 ml-1 inline-block" style="background-color: {{ $attrValue }};" title="{{ $attrValue }}"></span>
                                    @else
                                        <span class="ml-1">{{ $attrValue }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <p class="text-sm text-gray-500 mt-0.5">৳{{ number_format($item['price'] ?? 0, 0) }} each</p>
                </div>
                {{-- Remove --}}
                <form action="{{ route('cart.remove', $productId) }}" method="post" class="flex-shrink-0">
                    @csrf
                    @if($variantId)
                        <input type="hidden" name="variant_id" value="{{ $variantId }}">
                    @endif
                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>

            {{-- Quantity & Subtotal --}}
            <div class="flex items-center justify-between mt-3">
                <form action="{{ route('cart.update', $productId) }}" method="post" class="flex items-center">
                    @csrf
                    @if($variantId)
                        <input type="hidden" name="variant_id" value="{{ $variantId }}">
                    @endif
                    <div class="flex items-center border border-gray-200 rounded-lg">
                        <button type="submit" name="quantity" value="{{ max(1, $itemQty - 1) }}" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-l-lg text-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span class="w-9 sm:w-10 h-8 sm:h-9 flex items-center justify-center border-x border-gray-200 text-sm font-medium">{{ $itemQty }}</span>
                        <button type="submit" name="quantity" value="{{ $itemQty + 1 }}" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-r-lg text-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </form>
                <span class="text-base sm:text-lg font-bold text-gray-900">৳{{ number_format($itemSubtotal, 0) }}</span>
            </div>
        </div>
    </div>
</div>
