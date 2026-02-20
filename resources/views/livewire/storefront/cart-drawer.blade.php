<div>
    {{-- Overlay --}}
    @if ($isOpen)
        <div class="fixed inset-0 bg-black/60 z-[10000]" wire:click="close"></div>
    @endif

    {{-- Drawer --}}
    <div class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-[10001] transform transition-transform duration-300 {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}">
        <div class="flex flex-col h-full">
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Shopping Cart ({{ $itemCount }})</h2>
                <button wire:click="close" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-6">
                @if (empty($items))
                    <div class="text-center py-12">
                        <span class="text-5xl mb-4 block">🛒</span>
                        <p class="text-gray-500">Your cart is empty</p>
                        <a href="{{ route('catalog') }}" wire:navigate wire:click="close" class="mt-4 inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                            Start Shopping
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($items as $key => $item)
                            <div class="flex gap-4 pb-4 border-b border-gray-100">
                                {{-- Product Image --}}
                                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if (!empty($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-2xl">🧸</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 truncate">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-primary-dark font-bold mt-1">@price($item['price'])</p>

                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-2 mt-2">
                                        <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                            class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                            class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Remove Button --}}
                                <button wire:click="removeItem('{{ $key }}')" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            @if (!empty($items))
                <div class="border-t border-gray-200 p-6 space-y-4">
                    {{-- Subtotal --}}
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span class="text-gray-900">Subtotal:</span>
                        <span class="text-primary-dark">@price($subtotal)</span>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2">
                        <a href="{{ route('checkout.show') }}" wire:navigate wire:click="close"
                            class="block w-full py-3 bg-primary text-white text-center font-bold rounded-lg hover:bg-primary-dark transition">
                            Checkout
                        </a>
                        <a href="{{ route('cart.index') }}" wire:navigate wire:click="close"
                            class="block w-full py-3 border-2 border-primary text-primary text-center font-bold rounded-lg hover:bg-primary hover:text-white transition">
                            View Cart
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
