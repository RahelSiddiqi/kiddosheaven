@extends('layouts.app')

@section('title', 'Shopping Cart — Kiddo\'s Heaven')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <p class="text-gray-500 mt-1 text-sm">Review your items before checkout</p>
    </div>

    @php
        $cartItems = isset($cart['items']) ? collect($cart['items']) : collect();
        $itemCount = $cartItems->count();
        $cartSubtotal = $cart['subtotal'] ?? 0;
        $cartTotal = $cart['total'] ?? 0;
        $cartDiscount = $cart['discount'] ?? 0;
    @endphp

    @if ($itemCount > 0)
        <div class="flex flex-col lg:flex-row gap-6 md:gap-8">
            {{-- Cart Items --}}
            <div class="flex-1">
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        @foreach ($cartItems as $key => $item)
                            <x-shop.cart.item :item="$item" :item-key="$key" />
                        @endforeach
                    </div>

                    {{-- Continue Shopping --}}
                    <div class="px-4 md:px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 text-primary font-medium hover:text-primary-dark transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="lg:w-80 xl:w-96 shrink-0 order-first lg:order-last">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

                    {{-- Promo Code --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Have a promo code?</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Enter code"
                                class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">Apply</button>
                        </div>
                    </div>

                    {{-- Summary Lines --}}
                    <div class="space-y-3 text-sm mb-5">
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

                    {{-- Checkout Button --}}
                    <a href="{{ route('checkout.show') }}"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/25 mb-4">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    {{-- Trust Badges --}}
                    <div class="flex items-center justify-center gap-4 text-gray-400">
                        <div class="flex items-center gap-1 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Secure
                        </div>
                        <div class="flex items-center gap-1 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            COD Available
                        </div>
                    </div>

                    {{-- Delivery Info --}}
                    <div class="mt-5 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            <div class="text-xs text-gray-600">
                                <p class="font-medium text-gray-800">Free delivery on orders over 500</p>
                                <p class="mt-0.5">Estimated: 2-4 business days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart --}}
        <x-shop.ui.empty-state
            icon="cart"
            title="Your cart is empty"
            message="Looks like you haven't added any toys to your cart yet. Start shopping to find the perfect gift for your little one!"
            action-url="{{ route('catalog') }}"
            action-label="Start Shopping" />
    @endif
@endsection
