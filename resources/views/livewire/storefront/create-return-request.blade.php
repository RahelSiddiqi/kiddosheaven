<div>
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li>›</li>
            <li><a href="{{ route('account') }}" wire:navigate class="hover:text-primary">Account</a></li>
            <li>›</li>
            <li><a href="{{ route('customer.orders.index') }}" wire:navigate class="hover:text-primary">Orders</a></li>
            <li>›</li>
            <li><a href="{{ route('customer.orders.show', $order->id) }}" wire:navigate class="hover:text-primary">#{{ $order->order_number }}</a></li>
            <li>›</li>
            <li class="text-gray-800 font-medium">Return Request</li>
        </ol>
    </nav>

    <h1 class="text-2xl font-bold mb-6">Return Items from Order #{{ $order->order_number }}</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-800 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
    @endif

    <form wire:submit="submit" class="space-y-6 max-w-2xl">

        {{-- Return type --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold mb-4">What would you like to do?</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach(['refund' => ['label' => 'Get a Refund', 'icon' => '💰'], 'exchange' => ['label' => 'Exchange Item', 'icon' => '🔄'], 'store_credit' => ['label' => 'Store Credit', 'icon' => '🎁']] as $value => $opt)
                <label class="flex flex-col items-center border rounded-lg p-4 cursor-pointer transition-colors {{ $type === $value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <input wire:model.live="type" type="radio" value="{{ $value }}" class="sr-only">
                    <span class="text-2xl mb-1">{{ $opt['icon'] }}</span>
                    <span class="text-sm font-medium text-center">{{ $opt['label'] }}</span>
                </label>
                @endforeach
            </div>
            @error('type') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold mb-4">Select items to return</h2>
            @error('items') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="border rounded-lg p-4 {{ $items[$item->id]['selected'] ?? false ? 'border-indigo-400 bg-indigo-50/30' : 'border-gray-200' }}">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input wire:model.live="items.{{ $item->id }}.selected" type="checkbox" class="mt-1 rounded">
                        <div class="flex-1">
                            <p class="font-medium">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} · ৳{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                    </label>

                    @if($items[$item->id]['selected'] ?? false)
                    <div class="mt-3 pt-3 border-t grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1">Return Qty</label>
                            <input wire:model="items.{{ $item->id }}.quantity" type="number"
                                   min="1" max="{{ $item->quantity }}" class="input w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Condition</label>
                            <select wire:model="items.{{ $item->id }}.condition" class="input w-full text-sm">
                                <option value="new">Unopened / New</option>
                                <option value="like_new">Like New</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium mb-1">Reason</label>
                            <select wire:model="items.{{ $item->id }}.reason" class="input w-full text-sm">
                                <option value="defective">Defective / Damaged</option>
                                <option value="wrong_item">Wrong Item Sent</option>
                                <option value="changed_mind">Changed My Mind</option>
                                <option value="not_as_expected">Not As Expected</option>
                                <option value="size_issue">Wrong Size / Fit</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Reason --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold mb-4">Reason for return</h2>
            <textarea wire:model="reason" rows="3" class="input w-full"
                      placeholder="Please describe the issue..."></textarea>
            @error('reason') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Additional notes (optional)</label>
                <textarea wire:model="customer_notes" rows="2" class="input w-full"
                          placeholder="Any other information..."></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Submit Return Request</span>
                <span wire:loading>Submitting...</span>
            </button>
            <a href="{{ route('customer.orders.show', $order->id) }}" wire:navigate class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
