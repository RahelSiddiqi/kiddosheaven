<div>
    {{-- Status Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach (['active' => 'green', 'trialing' => 'blue', 'past_due' => 'yellow', 'canceled' => 'gray'] as $s => $color)
            <button wire:click="$set('status', '{{ $status === $s ? '' : $s }}')"
                class="rounded-xl border p-4 text-left transition-all
                {{ $status === $s ? 'ring-2 ring-blue-500 border-blue-200' : 'border-gray-200 dark:border-gray-700' }}
                bg-white dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $s) }}</p>
                <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ $statusCounts[$s] ?? 0 }}</p>
            </button>
        @endforeach
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:px-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or email..."
                class="h-9 w-full max-w-xs rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @if ($status)
                <button wire:click="$set('status', '')" class="text-xs text-blue-500 hover:underline">
                    Clear filter: {{ str_replace('_', ' ', $status) }}
                </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">User</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Plan (Stripe Price)</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Status</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Trial Ends</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Renews / Ends</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Qty</th>
                        <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($subscriptions as $sub)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $sub->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $sub->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $sub->stripe_price ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $sub->type }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ match($sub->stripe_status) {
                                        'active'   => 'bg-green-100 text-green-700',
                                        'trialing' => 'bg-blue-100 text-blue-700',
                                        'past_due' => 'bg-yellow-100 text-yellow-700',
                                        'canceled' => 'bg-gray-100 text-gray-500',
                                        default    => 'bg-gray-100 text-gray-500',
                                    } }}">
                                    {{ str_replace('_', ' ', ucfirst($sub->stripe_status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $sub->trial_ends_at ? $sub->trial_ends_at->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $sub->ends_at ? $sub->ends_at->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $sub->quantity }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if (!in_array($sub->stripe_status, ['canceled']))
                                    <button wire:click="cancel({{ $sub->id }})" wire:confirm="Cancel this subscription?"
                                        class="text-xs text-red-500 hover:underline">Cancel</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-400">No subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $subscriptions->links() }}</div>
        @endif
    </div>
</div>
