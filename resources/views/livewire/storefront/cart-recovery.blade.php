<div class="min-h-screen flex items-center justify-center px-4 py-16 bg-gray-50 dark:bg-gray-950">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-lg p-8 text-center">
        @if ($invalid)
            <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Link Expired</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">This cart recovery link is no longer valid or has already been used.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Continue Shopping
            </a>
        @elseif ($recovered)
            <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h10a1 1 0 00.95-.68L19 13M7 13l-1.35 2.7M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Your cart is ready!</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                We've restored your {{ count(json_decode($cart->cart_data, true) ?? []) }} item(s). Complete your order before they sell out.
            </p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('cart') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    View Cart
                </a>
                <a href="{{ route('checkout') }}" class="inline-flex items-center justify-center rounded-lg border border-blue-600 px-6 py-2.5 text-sm font-medium text-blue-600 hover:bg-blue-50">
                    Checkout Now
                </a>
            </div>
        @endif
    </div>
</div>
