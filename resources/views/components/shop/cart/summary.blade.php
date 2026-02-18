@props(['cart', 'showCheckoutButton' => true, 'showItems' => false])

@php
    $cartItems = isset($cart['items']) ? collect($cart['items']) : collect();
    $itemCount = $cartItems->count();
    $cartSubtotal = $cart['subtotal'] ?? 0;
    $cartDiscount = $cart['discount'] ?? 0;
    $cartTotal = $cartSubtotal - $cartDiscount;
@endphp

<div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 sticky top-24">
    <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

    {{-- Mini items list --}}
    @if ($showItems && $cartItems->isNotEmpty())
        <div class="space-y-3 mb-5 max-h-56 overflow-y-auto">
            @foreach ($cartItems as $item)
                @php $img = $item['image'] ?? null; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
                        @if ($img)
                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-lg">🧸</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-xs truncate">{{ $item['name'] }}</p>
                        <p class="text-xs text-gray-400">Qty: {{ $item['quantity'] ?? 1 }}</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">৳{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0) }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-gray-100 mb-5"></div>
    @endif

    {{-- Summary lines --}}
    <div class="space-y-3 text-sm">
        <div class="flex justify-between text-gray-600">
            <span>Subtotal ({{ $itemCount }} {{ Str::plural('item', $itemCount) }})</span>
            <span>৳{{ number_format($cartSubtotal, 0) }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Shipping</span>
            <span class="text-green-600 font-medium">Free</span>
        </div>
        @if ($cartDiscount > 0)
            <div class="flex justify-between text-green-600">
                <span>Discount</span>
                <span>-৳{{ number_format($cartDiscount, 0) }}</span>
            </div>
        @endif
        <div class="border-t border-gray-100 pt-3 flex justify-between text-lg font-bold text-gray-900">
            <span>Total</span>
            <span>৳{{ number_format($cartTotal, 0) }}</span>
        </div>
    </div>

    {{-- Checkout button --}}
    @if ($showCheckoutButton)
        <a href="{{ route('checkout.show') }}"
            class="mt-5 w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
            Proceed to Checkout
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    @endif

    {{-- Delivery info --}}
    <div class="mt-5 p-3 bg-gray-50 rounded-lg">
        <div class="flex items-start gap-2.5">
            <svg class="w-4 h-4 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
            <div class="text-xs text-gray-600">
                <p class="font-medium text-gray-800">Free delivery on orders over ৳500</p>
                <p class="mt-0.5">Estimated: 2-4 business days</p>
            </div>
        </div>
    </div>
</div>
