<div class="max-w-2xl mx-auto py-12 px-4">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Subscription</h1>
        <p class="text-gray-500 mt-1">Manage your Kiddos Heaven store plan.</p>
    </div>

    @if (!$subscription)
        {{-- No subscription --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">No Active Plan</h2>
            <p class="text-gray-500 mb-6">You don't have an active subscription yet. Choose a plan to get started.</p>
            <a href="{{ route('merchant.upgrade') }}" wire:navigate
                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition">
                View Plans
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>

    @else
        {{-- Active/Past subscription card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Status banner --}}
            @php
                $statusClasses = [
                    'active'    => 'bg-green-50 border-b border-green-100 text-green-800',
                    'trialing'  => 'bg-blue-50 border-b border-blue-100 text-blue-800',
                    'past_due'  => 'bg-yellow-50 border-b border-yellow-100 text-yellow-800',
                    'paused'    => 'bg-gray-50 border-b border-gray-100 text-gray-700',
                    'canceled'  => 'bg-red-50 border-b border-red-100 text-red-800',
                ];
                $statusIcons = [
                    'active'   => '✅',
                    'trialing' => '🔵',
                    'past_due' => '⚠️',
                    'paused'   => '⏸️',
                    'canceled' => '❌',
                ];
                $statusLabels = [
                    'active'    => 'Active',
                    'trialing'  => 'Free Trial',
                    'past_due'  => 'Payment Past Due',
                    'paused'    => 'Paused',
                    'canceled'  => 'Cancelled',
                ];
                $cls   = $statusClasses[$subscription->stripe_status] ?? 'bg-gray-50 border-b border-gray-100 text-gray-700';
                $icon  = $statusIcons[$subscription->stripe_status] ?? '•';
                $label = $statusLabels[$subscription->stripe_status] ?? ucfirst($subscription->stripe_status);
            @endphp

            <div class="px-6 py-4 {{ $cls }} flex items-center justify-between">
                <span class="font-semibold">{{ $icon }} {{ $label }}</span>
                @if ($subscription->stripe_status === 'trialing' && $subscription->trial_ends_at)
                    <span class="text-sm">Trial ends {{ $subscription->trial_ends_at->diffForHumans() }}</span>
                @elseif ($subscription->stripe_status === 'canceled' && $subscription->ends_at)
                    <span class="text-sm">Cancelled {{ $subscription->ends_at->format('M d, Y') }}</span>
                @endif
            </div>

            {{-- Plan details --}}
            <div class="p-6 space-y-5">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Plan</p>
                        <p class="text-lg font-bold text-gray-900 capitalize">
                            {{ ucwords(str_replace(['-','_'], ' ', $subscription->type ?? 'Standard')) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Billing Price ID</p>
                        <p class="text-sm font-mono text-gray-700 truncate">
                            {{ $subscription->stripe_price ?? '—' }}
                        </p>
                    </div>

                    @if ($subscription->trial_ends_at && $subscription->trial_ends_at->isFuture())
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-xs text-blue-500 uppercase tracking-wide font-medium mb-1">Trial Ends</p>
                        <p class="text-lg font-bold text-blue-800">{{ $subscription->trial_ends_at->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if ($subscription->ends_at)
                    <div class="bg-red-50 rounded-xl p-4">
                        <p class="text-xs text-red-500 uppercase tracking-wide font-medium mb-1">
                            {{ $subscription->stripe_status === 'canceled' ? 'Cancelled On' : 'Access Until' }}
                        </p>
                        <p class="text-lg font-bold text-red-800">{{ $subscription->ends_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
                    @if ($subscription->isActive())
                        @if (!$showCancelConfirm)
                            <button type="button" wire:click="confirmCancel"
                                class="px-5 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition">
                                Cancel Subscription
                            </button>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl w-full">
                                <p class="text-red-700 text-sm flex-1">Are you sure? Your access will end immediately.</p>
                                <button type="button" wire:click="cancelSubscription" wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                                    <span wire:loading.remove wire:target="cancelSubscription">Yes, Cancel</span>
                                    <span wire:loading wire:target="cancelSubscription">...</span>
                                </button>
                                <button type="button" wire:click="$set('showCancelConfirm', false)"
                                    class="px-4 py-2 text-gray-600 text-sm font-semibold hover:text-gray-900">
                                    Keep Plan
                                </button>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('merchant.upgrade') }}" wire:navigate
                            class="px-5 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition">
                            Reactivate / Upgrade Plan
                        </a>
                    @endif

                    <a href="{{ route('merchant.dashboard') }}" wire:navigate
                        class="px-5 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Merchant Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- FAQ / Help --}}
        <div class="mt-6 text-center text-sm text-gray-500">
            Have questions about your plan?
            <a href="{{ route('contact') }}" wire:navigate class="text-primary hover:underline font-medium ml-1">Contact support</a>
        </div>
    @endif
</div>
