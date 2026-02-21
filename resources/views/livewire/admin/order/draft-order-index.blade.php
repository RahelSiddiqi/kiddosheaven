<div>
    <div class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Draft Orders</h2>
                <p class="text-sm text-gray-500 mt-0.5">Create orders on behalf of customers</p>
            </div>
            <button wire:click="$set('showCreate', true)"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                New Draft
            </button>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 mb-3 sm:px-6">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by draft number, name or email..."
                    class="h-9 w-full max-w-xs rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-200 border-y dark:border-gray-700">
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Draft #</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Customer</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Items</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Total</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Payment</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Status</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Created</th>
                            <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($drafts as $draft)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-4 py-3">
                                    <code class="text-xs font-mono text-gray-800 dark:text-gray-300">{{ $draft->draft_number }}</code>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $draft->customer_name ?? '—' }}</p>
                                    @if ($draft->customer_email)
                                        <p class="text-xs text-gray-400">{{ $draft->customer_email }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $draft->itemCount() }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($draft->total_amount, 0) }}</td>
                                <td class="px-4 py-3 text-xs uppercase text-gray-500">{{ $draft->payment_method }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ match($draft->status) {
                                            'open'         => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'invoice_sent' => 'bg-purple-100 text-purple-700',
                                            'completed'    => 'bg-green-100 text-green-700',
                                            'cancelled'    => 'bg-gray-100 text-gray-500',
                                            default        => 'bg-gray-100 text-gray-500',
                                        } }}">
                                        {{ str_replace('_', ' ', ucfirst($draft->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $draft->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    @if ($draft->status === 'open')
                                        <button wire:click="cancel({{ $draft->id }})" wire:confirm="Cancel this draft?"
                                            class="text-xs text-red-500 hover:underline">Cancel</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-sm text-gray-400">No draft orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($drafts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $drafts->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Create Draft Modal --}}
    @if ($showCreate)
        <div class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 px-4 pt-10 overflow-y-auto" wire:click.self="$set('showCreate', false)">
            <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 shadow-xl mb-10">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Draft Order</h3>
                    <button wire:click="$set('showCreate', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="px-6 py-4 space-y-5">
                    {{-- Customer Info --}}
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">Customer</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <input wire:model="customer_name" type="text" placeholder="Name"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            <input wire:model="customer_email" type="email" placeholder="Email"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            <input wire:model="customer_phone" type="text" placeholder="Phone"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>

                    {{-- Product Search --}}
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">Products</h4>
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="productSearch"
                                   wire:updated.productSearch="searchProducts"
                                   type="text" placeholder="Search by product name or SKU…"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">

                            @if (! empty($productResults))
                                <div class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 max-h-48 overflow-y-auto">
                                    @foreach ($productResults as $p)
                                        <button type="button" wire:click="addLineItem({{ $p['id'] }})"
                                            class="flex w-full items-center justify-between px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-white/[0.04] text-left transition-colors">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $p['name'] }}</p>
                                                @if ($p['sku'])
                                                    <p class="text-xs text-gray-400 font-mono">{{ $p['sku'] }}</p>
                                                @endif
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">৳{{ number_format($p['price'], 0) }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Line Items --}}
                        @if (! empty($lineItems))
                            <div class="mt-3 rounded-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Qty</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Price</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                                            <th class="w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                        @foreach ($lineItems as $i => $item)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $item['name'] }}</td>
                                                <td class="px-3 py-2">
                                                    <input wire:model.live="lineItems.{{ $i }}.qty" type="number" min="1"
                                                        class="w-16 rounded border border-gray-200 px-2 py-1 text-sm text-center dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2 text-right text-sm text-gray-600 dark:text-gray-400">৳{{ number_format($item['price'], 0) }}</td>
                                                <td class="px-3 py-2 text-right text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($item['total'] ?? $item['qty'] * $item['price'], 0) }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <button wire:click="removeLineItem({{ $i }})" class="text-gray-300 hover:text-red-500 transition-colors">✕</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">Subtotal</td>
                                            <td colspan="2" class="px-3 py-2 text-right text-sm font-bold text-gray-900 dark:text-white">৳{{ number_format($this->subtotal(), 0) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Payment & Notes --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                            <select wire:model="payment_method" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                <option value="cod">Cash on Delivery</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="stripe">Card</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Note</label>
                            <input wire:model="note" type="text" placeholder="Internal note..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showCreate', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveDraft" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create Draft</button>
                </div>
            </div>
        </div>
    @endif
</div>
