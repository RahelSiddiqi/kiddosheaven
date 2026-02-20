<div class="space-y-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li><a href="{{ route('account') }}" wire:navigate class="hover:text-primary">Account</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li><a href="{{ route('customer.orders.index') }}" wire:navigate class="hover:text-primary">Orders</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li class="text-gray-800 font-medium">#{{ $order->order_number }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
            <p class="text-gray-500 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <span @class([
            'px-4 py-2 rounded-full text-sm font-bold self-start',
            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
            'bg-blue-100 text-blue-700' => $order->status === 'processing',
            'bg-green-100 text-green-700' => $order->status === 'delivered',
            'bg-red-100 text-red-700' => $order->status === 'cancelled',
            'bg-purple-100 text-purple-700' => $order->status === 'shipped',
        ])>{{ ucfirst($order->status) }}</span>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                            <div class="flex-shrink-0 w-20 h-20 rounded-lg bg-gray-100 overflow-hidden">
                                @if ($item->product && $item->product->primary_image)
                                    <img src="{{ asset($item->product->primary_image) }}" alt="{{ $item->product_name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-3xl">🧸</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                @if ($item->product)
                                    <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                                        class="font-semibold text-gray-900 hover:text-primary">{{ $item->product_name }}</a>
                                @else
                                    <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">
                                    Qty: {{ $item->quantity }} × @price($item->price)
                                </p>
                            </div>
                            <p class="font-bold text-gray-900">@price($item->subtotal)</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Customer & Shipping Info --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-3">Customer Information</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><span class="font-medium text-gray-900">Name:</span> {{ $order->customer_name }}</p>
                        <p><span class="font-medium text-gray-900">Email:</span> {{ $order->customer_email }}</p>
                        <p><span class="font-medium text-gray-900">Phone:</span> {{ $order->customer_phone }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-3">Shipping Address</h3>
                    <div class="text-sm text-gray-600">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">@price($order->subtotal)</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span class="font-semibold">@price($order->tax ?? 0)</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-semibold {{ ($order->shipping ?? 0) == 0 ? 'text-green-600' : '' }}">
                            {{ ($order->shipping ?? 0) == 0 ? 'FREE' : '৳' . number_format($order->shipping, 2) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-primary-dark">@price($order->total)</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Payment:</span>
                        <span class="font-semibold text-gray-900">
                            @if ($order->payment_method === 'cod') Cash on Delivery
                            @elseif ($order->payment_method === 'bkash') bKash
                            @elseif ($order->payment_method === 'card') Credit/Debit Card
                            @else {{ ucfirst($order->payment_method ?? 'N/A') }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Cancel Button --}}
                @if (in_array($order->status, ['pending', 'processing']))
                    <button wire:click="cancelOrder"
                        wire:confirm="Are you sure you want to cancel this order?"
                        class="w-full px-4 py-3 rounded-lg border-2 border-red-200 text-red-600 font-bold hover:bg-red-50 transition">
                        Cancel Order
                    </button>
                @endif

                @if ($order->notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                        <p class="text-sm text-gray-600"><strong>Notes:</strong> {{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Back to Orders --}}
    <div class="flex gap-4">
        <a href="{{ route('customer.orders.index') }}" wire:navigate
            class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-primary hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
        <a href="{{ route('catalog') }}" wire:navigate
            class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition">
            Continue Shopping
        </a>
    </div>
</div>
