<div class="min-h-[calc(100vh-65px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl">
        {{-- Progress Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-center">
                @foreach ([1 => 'Store Info', 2 => 'Choose Plan', 3 => 'Launch'] as $stepNum => $stepLabel)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div @class([
                                'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-colors',
                                'bg-blue-600 border-blue-600 text-white' => $step >= $stepNum,
                                'bg-white border-gray-300 text-gray-500' => $step < $stepNum,
                            ])>
                                @if ($step > $stepNum)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    {{ $stepNum }}
                                @endif
                            </div>
                            <span @class([
                                'mt-2 text-xs font-medium',
                                'text-blue-600' => $step >= $stepNum,
                                'text-gray-500' => $step < $stepNum,
                            ])>{{ $stepLabel }}</span>
                        </div>
                        @if ($stepNum < 3)
                            <div @class([
                                'w-16 sm:w-24 h-0.5 mx-2',
                                'bg-blue-600' => $step > $stepNum,
                                'bg-gray-300' => $step <= $stepNum,
                            ])></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Card Container --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            {{-- Step 1: Store Info --}}
            @if ($step === 1)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Set up your store</h2>
                    <p class="text-gray-500 mb-6">Choose a name and subdomain for your online store.</p>

                    <div class="space-y-5">
                        <div>
                            <label for="storeName" class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                            <input
                                type="text"
                                id="storeName"
                                wire:model="storeName"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="My Awesome Store"
                            >
                            @error('storeName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-1">Subdomain</label>
                            <div class="flex">
                                <input
                                    type="text"
                                    id="subdomain"
                                    wire:model.live="subdomain"
                                    wire:change="checkSubdomain"
                                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="mystore"
                                >
                                <span class="inline-flex items-center px-4 py-2.5 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-lg text-sm">
                                    .kiddosheaven.com
                                </span>
                            </div>
                            @error('subdomain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($subdomainError)
                                <p class="mt-1 text-sm text-red-600">{{ $subdomainError }}</p>
                            @elseif ($subdomain && strlen($subdomain) >= 3 && !$errors->has('subdomain'))
                                <p class="mt-1 text-sm text-green-600">This subdomain is available!</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button
                            wire:click="nextStep"
                            class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 2: Plan Selection --}}
            @if ($step === 2)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose your plan</h2>
                    <p class="text-gray-500 mb-6">Start with a 14-day free trial. No credit card required.</p>

                    <div class="grid gap-4">
                        @foreach ($plans as $plan)
                            <div
                                wire:click="$set('selectedPlanId', {{ $plan->id }})"
                                @class([
                                    'relative p-5 border-2 rounded-xl cursor-pointer transition-all',
                                    'border-blue-600 ring-2 ring-blue-600 ring-opacity-50 bg-blue-50' => $selectedPlanId === $plan->id,
                                    'border-gray-200 hover:border-gray-300' => $selectedPlanId !== $plan->id,
                                ])
                            >
                                @if ($plan->slug === 'growth')
                                    <div class="absolute -top-3 left-4 px-3 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded-full">
                                        Most Popular
                                    </div>
                                @endif

                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-2xl font-bold text-gray-900">${{ number_format($plan->price_cents / 100) }}</span>
                                                <span class="text-gray-500 text-sm">/month</span>
                                            </div>
                                        </div>

                                        @if ($plan->features)
                                            <ul class="mt-3 space-y-1.5">
                                                @foreach (array_slice($plan->features, 0, 5) as $featureKey => $featureValue)
                                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        @if (is_bool($featureValue))
                                                            {{ ucwords(str_replace('_', ' ', $featureKey)) }}
                                                        @else
                                                            {{ ucwords(str_replace('_', ' ', $featureKey)) }}: {{ $featureValue }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>

                                    <div @class([
                                        'w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-1',
                                        'border-blue-600 bg-blue-600' => $selectedPlanId === $plan->id,
                                        'border-gray-300' => $selectedPlanId !== $plan->id,
                                    ])>
                                        @if ($selectedPlanId === $plan->id)
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('selectedPlanId')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-8 flex justify-between">
                        <button
                            wire:click="prevStep"
                            class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                        >
                            Back
                        </button>
                        <button
                            wire:click="nextStep"
                            class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 3: Confirm & Launch --}}
            @if ($step === 3)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Review and launch</h2>
                    <p class="text-gray-500 mb-6">You're almost there! Review your store details below.</p>

                    <div class="bg-gray-50 rounded-lg p-5 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Store Name</span>
                            <span class="font-medium text-gray-900">{{ $storeName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Store URL</span>
                            <span class="font-medium text-blue-600">{{ strtolower($subdomain) }}.kiddosheaven.com</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan</span>
                            <span class="font-medium text-gray-900">
                                {{ $plans->find($selectedPlanId)?->name ?? 'Not selected' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Trial Period</span>
                            <span class="font-medium text-green-600">14 days free</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="agreeToTerms"
                                class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                        @error('agreeToTerms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button
                            wire:click="prevStep"
                            class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                        >
                            Back
                        </button>
                        <button
                            wire:click="launch"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-8 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                        >
                            <span wire:loading.remove wire:target="launch">Launch My Store</span>
                            <span wire:loading wire:target="launch">Creating store...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
