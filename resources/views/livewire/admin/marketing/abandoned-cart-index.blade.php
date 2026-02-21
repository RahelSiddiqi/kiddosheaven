<div>
    <!-- Stats row -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-xs text-gray-500 dark:text-gray-400">Abandoned (Active)</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-xs text-gray-500 dark:text-gray-400">Recovered</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['recovered']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <p class="text-xs text-gray-500 dark:text-gray-400">Recovered Revenue</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">৳{{ number_format($stats['revenue'], 0) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Abandoned Carts</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">3-stage automated recovery</p>
            </div>
            <div class="flex gap-3">
                <select wire:model.live="status" class="h-9 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="recovered">Recovered</option>
                    <option value="expired">Expired</option>
                </select>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Email or token..."
                        class="h-9 rounded-lg border border-gray-300 bg-transparent pl-3 pr-4 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Customer</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Items</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Subtotal</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Reminders Sent</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Abandoned</th>
                        <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($carts as $cart)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cart->email ?? ($cart->user?->name ?? '—') }}</p>
                                @if ($cart->phone)
                                    <p class="text-xs text-gray-400">{{ $cart->phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $cart->itemCount() }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($cart->subtotal, 0) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $cart->status === 'active' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                       ($cart->status === 'recovered' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                       'bg-gray-100 text-gray-500') }}">
                                    {{ ucfirst($cart->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    @foreach ([1, 2, 3] as $n)
                                        <span class="h-5 w-5 rounded text-[10px] font-bold flex items-center justify-center
                                            {{ $cart->{"reminder_{$n}_sent_at"} ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-700' }}">
                                            {{ $n }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $cart->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                @if ($cart->status === 'active' && $cart->email)
                                    @foreach ([1, 2, 3] as $n)
                                        @if (! $cart->{"reminder_{$n}_sent_at"})
                                            <button wire:click="sendReminder({{ $cart->id }}, {{ $n }})"
                                                class="text-xs text-blue-600 hover:underline mr-2">
                                                Send R{{ $n }}
                                            </button>
                                            @break
                                        @endif
                                    @endforeach
                                    <button wire:click="markExpired({{ $cart->id }})" class="text-xs text-gray-400 hover:underline">Expire</button>
                                @elseif ($cart->status === 'recovered')
                                    <span class="text-xs text-green-500">✓ Recovered</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-400">No abandoned carts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($carts->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $carts->links() }}</div>
        @endif
    </div>
</div>
