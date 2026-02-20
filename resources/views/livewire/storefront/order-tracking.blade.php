<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Page Header --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Track Your Order</h1>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">Enter your order ID to track the delivery status</p>
        </div>

        {{-- Search Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    wire:model="orderNumber"
                    wire:keydown.enter="track"
                    placeholder="Enter your order number (e.g., ORD-12345)"
                    class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/20 outline-none"
                />
                <button
                    wire:click="track"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-xl bg-[var(--color-primary)] text-white font-bold text-sm hover:bg-[var(--color-primary-dark)] transition whitespace-nowrap disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="track">Track Order</span>
                    <span wire:loading wire:target="track">Searching…</span>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-2">You can find your order ID in your confirmation email or My Orders page.</p>
        </div>

        {{-- Order Found --}}
        @if($order)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h2>
                            <x-ui.badge :variant="$order->status">{{ ucfirst($order->status) }}</x-ui.badge>
                        </div>
                        <p class="text-xs text-gray-500">Placed {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xl font-bold text-[var(--color-primary-dark)]">@price($order->total_amount)</p>
                        <p class="text-xs text-gray-500">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm">Order Progress</h3>
                    <div class="relative">
                        <div class="absolute left-4 sm:left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        @php
                            $steps = [
                                ['status' => 'pending',    'label' => 'Order Placed', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                                ['status' => 'processing', 'label' => 'Processing',   'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                                ['status' => 'shipped',    'label' => 'Shipped',      'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                                ['status' => 'delivered',  'label' => 'Delivered',    'icon' => 'M5 13l4 4L19 7'],
                            ];
                            $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3];
                            $currentIdx  = $statusOrder[$order->status] ?? -1;
                        @endphp
                        <div class="space-y-5">
                            @foreach($steps as $idx => $step)
                                @php $done = $idx <= $currentIdx; $active = $idx === $currentIdx; @endphp
                                <div class="relative flex items-start gap-4">
                                    <div @class(['relative z-10 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center shrink-0 transition-all', 'bg-[var(--color-primary)] text-white' => $done, 'bg-gray-100 text-gray-400' => !$done])>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}" />
                                        </svg>
                                        @if($active)
                                            <div class="absolute -inset-1 rounded-full border-2 border-[var(--color-primary)] animate-pulse"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 pt-1.5">
                                        <p @class(['font-medium text-sm', 'text-[var(--color-primary)]' => $active, 'text-gray-900' => $done && !$active, 'text-gray-400' => !$done])>
                                            {{ $step['label'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div>
                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Order Items</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 shrink-0">
                                    @if($item->product?->primary_image)
                                        <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl">🧸</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm truncate">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × @price($item->unit_price)</p>
                                </div>
                                <span class="font-semibold text-gray-800 text-sm">@price($item->quantity * $item->unit_price)</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($order->shipping_address)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-2 text-sm">Delivery Address</h3>
                        <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
                    </div>
                @endif
            </div>

        @elseif($searched && $orderNumber !== '')
            {{-- Not Found --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                <div class="text-5xl mb-4">😕</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Order Not Found</h3>
                <p class="text-gray-500 mb-6">We couldn't find an order with that ID. Please check and try again.</p>
                <a href="{{ route('account') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[var(--color-primary)] text-white font-bold text-sm hover:bg-[var(--color-primary-dark)] transition">
                    View My Orders
                </a>
            </div>

        @else
            {{-- Help tips --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="font-bold text-gray-900 mb-4">How to track your order</h3>
                <div class="space-y-4">
                    @foreach([['1','Find your order ID','Check your confirmation email or visit My Orders in your account.'],['2','Enter the order ID','Type your order number in the field above and click Track Order.'],['3','View delivery status','See the current status and delivery progress of your order.']] as [$num, $title, $desc])
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-full bg-[var(--color-primary-light)] flex items-center justify-center shrink-0">
                                <span class="text-[var(--color-primary)] font-bold text-sm">{{ $num }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Need help? <a href="{{ route('contact') }}" class="text-[var(--color-primary)] font-medium hover:text-[var(--color-primary-dark)]">Contact us</a></p>
                </div>
            </div>
        @endif

    </div>
</div>
