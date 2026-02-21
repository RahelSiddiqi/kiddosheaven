<div class="max-w-lg mx-auto py-12 px-4">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Check Gift Card Balance</h1>
        <p class="text-gray-500 mt-2">Enter your gift card code to see the remaining balance.</p>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="space-y-5">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Gift Card Code</label>
                <input
                    type="text"
                    id="code"
                    wire:model="code"
                    wire:keydown.enter="check"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 text-center text-lg tracking-widest font-mono uppercase"
                    placeholder="XXXX-XXXX-XXXX"
                    autocomplete="off"
                    maxlength="30"
                >
                @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button
                type="button"
                wire:click="check"
                wire:loading.attr="disabled"
                wire:target="check"
                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="check">Check Balance</span>
                <span wire:loading wire:target="check">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
            </button>
        </div>

        {{-- Error --}}
        @if ($checked && $errorMessage)
            <div class="mt-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        {{-- Success --}}
        @if ($checked && $cardInfo)
            <div class="mt-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-mono text-sm text-purple-700 font-medium">{{ $cardInfo['code'] }}</p>
                        <p class="text-xs text-purple-500">Gift Card</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-baseline justify-between">
                        <span class="text-sm text-purple-600">Remaining Balance</span>
                        <span class="text-3xl font-bold text-purple-800">@price($cardInfo['balance'])</span>
                    </div>

                    @if ($cardInfo['original'] != $cardInfo['balance'])
                    <div class="flex justify-between text-sm text-purple-500">
                        <span>Original Value</span>
                        <span>@price($cardInfo['original'])</span>
                    </div>
                    @endif

                    @if ($cardInfo['expires_at'])
                    <div class="flex justify-between text-sm {{ now()->gt(\Carbon\Carbon::parse($cardInfo['expires_at'])) ? 'text-red-600' : 'text-purple-500' }}">
                        <span>Expires</span>
                        <span>{{ $cardInfo['expires_at'] }}</span>
                    </div>
                    @else
                    <div class="flex justify-between text-sm text-purple-500">
                        <span>Expires</span>
                        <span>Never</span>
                    </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-purple-200">
                    <a href="{{ route('checkout.show') }}" wire:navigate
                        class="block w-full text-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition text-sm">
                        Use at Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        Lost your card? <a href="{{ route('contact') }}" wire:navigate class="underline hover:text-gray-600">Contact support</a>
    </p>
</div>
