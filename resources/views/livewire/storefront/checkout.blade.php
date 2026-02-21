<div class="space-y-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li><a href="{{ route('cart.index') }}" wire:navigate class="hover:text-primary">Cart</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li class="text-gray-800 font-medium">Checkout</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Checkout</h1>

    @error('order')
        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
            <span class="font-medium">{{ $message }}</span>
        </div>
    @enderror

    <form wire:submit="placeOrder" class="grid lg:grid-cols-3 gap-8">
        {{-- Checkout Form --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Customer Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Customer Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="customer_name" wire:model="customer_name"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Enter your full name">
                        @error('customer_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="customer_email" wire:model="customer_email"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="your@email.com">
                            @error('customer_email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" id="customer_phone" wire:model="customer_phone"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="+880 1xxx xxx xxx">
                            @error('customer_phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Address</h2>
                <div class="space-y-4">
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                        <textarea id="shipping_address" wire:model="shipping_address" rows="3"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="House/Flat no, Street name, Area"></textarea>
                        @error('shipping_address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                            <input type="text" id="shipping_city" wire:model="shipping_city"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Dhaka">
                            @error('shipping_city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">State/Division *</label>
                            <input type="text" id="shipping_state" wire:model="shipping_state"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Dhaka">
                            @error('shipping_state') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="shipping_zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code *</label>
                            <input type="text" id="shipping_zip" wire:model="shipping_zip"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="1200">
                            @error('shipping_zip') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select id="shipping_country" wire:model.live="shipping_country"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                            <option value="BD">Bangladesh 🇧🇩</option>
                            <option value="US">United States 🇺🇸</option>
                            <option value="GB">United Kingdom 🇬🇧</option>
                            <option value="CA">Canada 🇨🇦</option>
                            <option value="AU">Australia 🇦🇺</option>
                            <option value="IN">India 🇮🇳</option>
                            <option value="SG">Singapore 🇸🇬</option>
                            <option value="MY">Malaysia 🇲🇾</option>
                            <option value="AE">UAE 🇦🇪</option>
                            <option value="SA">Saudi Arabia 🇸🇦</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Method</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition {{ $payment_method === 'cod' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="payment_method" value="cod" class="w-5 h-5 text-primary">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">Cash on Delivery</div>
                            <div class="text-sm text-gray-600">Pay with cash when you receive your order</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition {{ $payment_method === 'bkash' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="payment_method" value="bkash" class="w-5 h-5 text-primary">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">bKash</div>
                            <div class="text-sm text-gray-600">Pay with bKash mobile wallet</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition {{ $payment_method === 'card' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="payment_method" value="card" class="w-5 h-5 text-primary">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">Credit/Debit Card</div>
                            <div class="text-sm text-gray-600">Pay securely with your card</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition {{ $payment_method === 'nagad' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="payment_method" value="nagad" class="w-5 h-5 text-primary">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">Nagad</div>
                            <div class="text-sm text-gray-600">Pay with Nagad mobile banking</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition {{ $payment_method === 'bank' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="payment_method" value="bank" class="w-5 h-5 text-primary">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">Bank Transfer</div>
                            <div class="text-sm text-gray-600">Pay via direct bank transfer</div>
                        </div>
                    </label>
                </div>
                @error('payment_method') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Discounts & Promotions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Discounts & Promotions</h2>
                <div class="space-y-4">

                    {{-- Coupon Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code</label>
                        @if ($couponApplied)
                            <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center gap-2 text-green-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="font-medium text-sm">{{ strtoupper($coupon_code) }} applied — -@price($couponDiscount)</span>
                                </div>
                                <button type="button" wire:click="removeCoupon" class="text-green-600 hover:text-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <input type="text" wire:model="coupon_code"
                                    class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                                    placeholder="Enter coupon code">
                                <button type="button" wire:click="applyCoupon"
                                    wire:loading.attr="disabled" wire:target="applyCoupon"
                                    class="px-5 py-3 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="applyCoupon">Apply</span>
                                    <span wire:loading wire:target="applyCoupon">...</span>
                                </button>
                            </div>
                            @error('coupon_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- Gift Card --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gift Card</label>
                        @if ($giftCardApplied)
                            <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="flex items-center gap-2 text-purple-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                                    <span class="font-medium text-sm">Gift card applied — -@price($giftCardDiscount)</span>
                                </div>
                                <button type="button" wire:click="removeGiftCard" class="text-purple-600 hover:text-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <input type="text" wire:model="gift_card_code"
                                    class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                                    placeholder="Enter gift card code">
                                <button type="button" wire:click="applyGiftCard"
                                    wire:loading.attr="disabled" wire:target="applyGiftCard"
                                    class="px-5 py-3 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="applyGiftCard">Apply</span>
                                    <span wire:loading wire:target="applyGiftCard">...</span>
                                </button>
                            </div>
                            @error('gift_card_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                </div>
            </div>

            {{-- Order Notes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Notes (Optional)</h2>
                <textarea wire:model="notes" rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Any special instructions for your order?"></textarea>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                {{-- Cart Items --}}
                <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                    @foreach ($cart['items'] as $item)
                        <div class="flex gap-3 pb-3 border-b border-gray-100">
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg bg-gray-100 overflow-hidden">
                                @if ($item['image'])
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">🧸</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-gray-900 line-clamp-2">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Qty: {{ $item['quantity'] }} x @price($item['price'])
                                </p>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm">
                                @price($item['price'] * $item['quantity'])
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Price Breakdown --}}
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal ({{ count($cart['items']) }} items)</span>
                        <span class="font-semibold">@price($subtotal)</span>
                    </div>
                    @if ($taxRate > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>VAT ({{ $taxRate }}%)</span>
                        <span class="font-semibold">@price($tax)</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-semibold {{ $shipping == 0 ? 'text-green-600' : '' }}">
                            @if ($shipping == 0)
                                FREE
                            @else
                                @price($shipping)
                            @endif
                        </span>
                    </div>
                    @if ($subtotal < 1000 && $shipping > 0)
                        <p class="text-xs text-gray-500">Add @price(1000 - $subtotal) more for free shipping</p>
                    @endif

                    @if (($couponDiscount ?? 0) > 0)
                        <div class="flex justify-between text-green-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                Coupon ({{ strtoupper($coupon_code) }})
                            </span>
                            <span class="font-semibold">-@price($couponDiscount)</span>
                        </div>
                    @endif

                    @if ($giftCardDiscount > 0)
                        <div class="flex justify-between text-purple-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                                Gift Card
                            </span>
                            <span class="font-semibold">-@price($giftCardDiscount)</span>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-primary-dark">@price($total)</span>
                    </div>
                    @if ($shipping_country !== 'BD')
                        <p class="text-xs text-gray-500 mt-1">Shipping to {{ $shipping_country }} · VAT {{ $taxRate }}%</p>
                    @endif
                </div>

                {{-- Place Order Button --}}
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="placeOrder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="placeOrder">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                    <span wire:loading wire:target="placeOrder">Processing...</span>
                </button>

                <p class="text-xs text-gray-500 text-center mt-4">
                    By placing your order, you agree to our Terms & Conditions
                </p>
            </div>
        </div>
    </form>
</div>
