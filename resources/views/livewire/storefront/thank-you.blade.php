<div class="max-w-3xl mx-auto space-y-8">
    {{-- Success Message --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Thank You for Your Order! 🎉</h1>
        <p class="text-lg text-gray-600 mb-6">
            Your order has been successfully placed and is being processed.
        </p>

        <div class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 rounded-lg">
            <span class="text-sm text-gray-600">Order Number:</span>
            <span class="text-lg font-bold text-primary-dark">{{ $order->order_number }}</span>
        </div>
    </div>

    {{-- Order Details --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Details</h2>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            {{-- Customer Info --}}
            <div>
                <h3 class="font-bold text-gray-900 mb-3">Customer Information</h3>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600"><span class="font-medium text-gray-900">Name:</span> {{ $order->customer_name }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-900">Email:</span> {{ $order->customer_email }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-900">Phone:</span> {{ $order->customer_phone }}</p>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div>
                <h3 class="font-bold text-gray-900 mb-3">Shipping Address</h3>
                <div class="text-sm text-gray-600">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">Order Items</h3>
            <div class="space-y-4">
                @foreach ($order->items as $item)
                    <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                        <div class="flex-shrink-0 w-20 h-20 rounded-lg bg-gray-100 overflow-hidden">
                            @if ($item->product && $item->product->primary_image)
                                <img src="{{ asset($item->product->primary_image) }}" alt="{{ $item->product_name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">🧸</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                Quantity: {{ $item->quantity }} × ৳{{ number_format($item->price, 2) }}
                            </p>
                        </div>
                        <p class="font-bold text-gray-900">৳{{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="border-t border-gray-200 pt-6 mt-6">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-semibold">৳{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax</span>
                    <span class="font-semibold">৳{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Shipping</span>
                    <span class="font-semibold {{ $order->shipping == 0 ? 'text-green-600' : '' }}">
                        {{ $order->shipping == 0 ? 'FREE' : '৳' . number_format($order->shipping, 2) }}
                    </span>
                </div>
            </div>
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <span class="text-lg font-bold text-gray-900">Total</span>
                <span class="text-2xl font-bold text-primary-dark">৳{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="bg-gray-50 rounded-lg p-4 mt-6">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Payment Method:</span>
                <span class="font-semibold text-gray-900">
                    @if ($order->payment_method === 'cod')
                        Cash on Delivery
                    @elseif ($order->payment_method === 'bkash')
                        bKash
                    @elseif ($order->payment_method === 'card')
                        Credit/Debit Card
                    @else
                        {{ ucfirst($order->payment_method) }}
                    @endif
                </span>
            </div>
        </div>

        @if ($order->notes)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                <p class="text-sm text-gray-600"><strong>Order Notes:</strong> {{ $order->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Next Steps --}}
    <div class="bg-primary/10 rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">What's Next?</h2>
        <ul class="space-y-3 text-gray-700">
            <li class="flex items-start gap-3">
                <svg class="w-6 h-6 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>You'll receive an order confirmation email at <strong>{{ $order->customer_email }}</strong></span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-6 h-6 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span>We'll pack and ship your order within 2-3 business days</span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-6 h-6 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Track your order status anytime using your order number</span>
            </li>
        </ul>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-4">
        @auth
            <a href="{{ route('customer.orders.show', $order->id) }}" wire:navigate
                class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Order Details
            </a>
        @endauth
        <a href="{{ route('catalog') }}" wire:navigate
            class="flex-1 flex items-center justify-center gap-2 px-6 py-4 border-2 border-primary text-primary font-bold rounded-xl hover:bg-primary hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            Continue Shopping
        </a>
    </div>

    {{-- Customer Support --}}
    <div class="text-center text-sm text-gray-600">
        <p>Need help with your order?</p>
        <a href="{{ route('contact') }}" wire:navigate class="text-primary hover:text-primary-dark font-semibold">
            Contact our support team
        </a>
    </div>
</div>
