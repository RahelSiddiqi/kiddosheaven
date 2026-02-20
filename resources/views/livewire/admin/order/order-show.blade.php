<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h1>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.badge :variant="$order->status">{{ ucfirst($order->status) }}</x-ui.badge>
            @if(in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']))
                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Invoice
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column: Order items + history --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Order Items --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 text-sm">Order Items</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 px-5 py-4">
                            <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 shrink-0">
                                @if($item->product?->primary_image)
                                    <img src="{{ asset('storage/' . $item->product->primary_image) }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">🧸</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $item->product_name ?? $item->product?->name }}</p>
                                @if($item->variant)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->variant->display_name ?? '' }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">SKU: {{ $item->product?->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold text-gray-800">@price($item->unit_price * $item->quantity)</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x @price($item->unit_price)</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 space-y-1.5">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>@price($order->subtotal ?? $order->total_amount)</span>
                    </div>
                    @if($order->shipping_amount ?? 0)
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span>@price($order->shipping_amount)</span>
                        </div>
                    @endif
                    @if($order->discount_amount ?? 0)
                        <div class="flex justify-between text-sm text-emerald-600">
                            <span>Discount</span>
                            <span>-@price($order->discount_amount)</span>
                        </div>
                    @endif
                    @if($order->tax_amount ?? 0)
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tax</span>
                            <span>@price($order->tax_amount)</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-1.5 border-t border-gray-200">
                        <span>Total</span>
                        <span>@price($order->total_amount)</span>
                    </div>
                </div>
            </div>

            {{-- Order Status History --}}
            @if($order->statusHistory && $order->statusHistory->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900 text-sm">Status History</h2>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach($order->statusHistory->sortByDesc('created_at') as $history)
                            <div class="flex items-start gap-3 text-sm">
                                <div class="w-2 h-2 rounded-full bg-[var(--color-primary)] mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ ucfirst($history->status) }}</p>
                                    @if($history->note) <p class="text-gray-500 text-xs mt-0.5">{{ $history->note }}</p> @endif
                                    <p class="text-gray-400 text-xs">{{ $history->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Order Notes --}}
            @if($order->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-800">
                    <p class="font-semibold mb-1">Order Notes</p>
                    <p>{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Right column: Customer, Addresses, Status Update --}}
        <div class="space-y-5">

            {{-- Customer Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 text-sm mb-3">Customer</h2>
                <div class="space-y-1.5 text-sm">
                    <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                    @if($order->customer_email)
                        <p class="text-gray-500">{{ $order->customer_email }}</p>
                    @endif
                    @if($order->customer_phone)
                        <p class="text-gray-500">{{ $order->customer_phone }}</p>
                    @endif
                    @if($order->user)
                        <a href="{{ route('admin.customers.show', $order->user->id) }}" class="text-xs text-[var(--color-primary)] hover:underline">View customer profile</a>
                    @endif
                </div>
            </div>

            {{-- Shipping Address --}}
            @if($order->shipping_address)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 text-sm mb-3">Shipping Address</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $order->shipping_address }}</p>
                </div>
            @endif

            {{-- Payment Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 text-sm mb-3">Payment</h2>
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Method</span>
                        <span class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $order->payment_method ?? 'cod') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="{{ ($order->payment_status ?? '') === 'paid' ? 'text-emerald-600' : 'text-amber-600' }} font-medium capitalize">
                            {{ $order->payment_status ?? 'unpaid' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 text-sm mb-3">Update Status</h2>
                <div class="space-y-3">
                    <select wire:model="newStatus" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    <textarea wire:model="statusNote" rows="2" placeholder="Optional note..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none resize-none"></textarea>

                    <button
                        wire:click="updateStatus"
                        wire:loading.attr="disabled"
                        @class([
                            'w-full py-2 rounded-lg text-sm font-semibold transition disabled:opacity-50',
                            'bg-red-500 text-white hover:bg-red-600' => $newStatus === 'cancelled',
                            'bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)]' => $newStatus !== 'cancelled',
                        ])
                    >
                        <span wire:loading.remove>Update Status</span>
                        <span wire:loading>Updating...</span>
                    </button>
                </div>
            </div>

            {{-- Mark as Shipped --}}
            @if(in_array($order->status, ['pending', 'processing']))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 text-sm mb-3">Mark as Shipped</h2>
                    <div class="space-y-3">
                        <div>
                            <input
                                type="text"
                                wire:model="trackingNumber"
                                placeholder="Enter tracking number..."
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none"
                            />
                            @error('trackingNumber')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button
                            wire:click="ship"
                            wire:loading.attr="disabled"
                            class="w-full py-2 rounded-lg bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 transition disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="ship">Ship Order</span>
                            <span wire:loading wire:target="ship">Processing...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
