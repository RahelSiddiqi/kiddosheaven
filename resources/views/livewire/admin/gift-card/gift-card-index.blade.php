<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Gift Cards</h2>
            <p class="text-sm text-gray-500 mt-0.5">Issue, track and top-up gift cards</p>
        </div>
        <button wire:click="openIssue"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            + Issue Card
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:px-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search code or email..."
                class="h-9 w-full max-w-xs rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <select wire:model.live="status" class="h-9 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="expired">Expired</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Code</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Issued To</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Balance</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Initial</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Expires</th>
                        <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs">Status</th>
                        <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($cards as $card)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3">
                                <code class="font-mono text-sm text-gray-900 dark:text-white">{{ $card->code }}</code>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $card->issuedTo?->name ?? '—' }}</p>
                                @if($card->issued_to_email)
                                    <p class="text-xs text-gray-400">{{ $card->issued_to_email }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-bold {{ $card->balance > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    ৳{{ number_format($card->balance, 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">৳{{ number_format($card->initial_balance, 0) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $card->expires_at ? $card->expires_at->format('d M Y') : 'Never' }}
                            </td>
                            <td class="px-4 py-3">
                                @if (!$card->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Inactive</span>
                                @elseif ($card->isExpired())
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-600">Expired</span>
                                @elseif ($card->balance <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-600">Depleted</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs space-x-3">
                                <button wire:click="viewTransactions({{ $card->id }})" class="text-blue-500 hover:underline">Txns</button>
                                <button wire:click="openTopUp({{ $card->id }})" class="text-purple-500 hover:underline">Top-up</button>
                                <button wire:click="toggle({{ $card->id }})" class="text-gray-400 hover:underline">
                                    {{ $card->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-400">No gift cards yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($cards->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $cards->links() }}</div>
        @endif
    </div>

    {{-- Issue Form Modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Issue Gift Card</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (৳)</label>
                        <input wire:model="amount" type="number" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Issue to email (optional)</label>
                        <input wire:model="issuedEmail" type="email" placeholder="customer@example.com"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Expires on (optional)</label>
                        <input wire:model="expiresAt" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Note</label>
                        <input wire:model="note" type="text" placeholder="Reason / occasion..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-5">
                    <button wire:click="$set('showForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="issueCard" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Issue Card</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Top-up Modal --}}
    @if ($showTopUp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showTopUp', false)">
            <div class="w-full max-w-xs rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Top-up Gift Card</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (৳)</label>
                    <input wire:model="topUpAmount" type="number" min="1" autofocus
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    @error('topUpAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-5">
                    <button wire:click="$set('showTopUp', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="applyTopUp" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">Top-up</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Transaction History Drawer --}}
    @if ($showTx)
        <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center bg-black/50 px-4" wire:click.self="$set('showTx', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6 max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Transaction History</h3>
                    <button wire:click="$set('showTx', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                @if ($txs->isEmpty())
                    <p class="text-sm text-center text-gray-400 py-6">No transactions for this card.</p>
                @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="py-2 text-left text-xs text-gray-500">Type</th>
                                <th class="py-2 text-left text-xs text-gray-500">Amount</th>
                                <th class="py-2 text-left text-xs text-gray-500">Note</th>
                                <th class="py-2 text-left text-xs text-gray-500">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach ($txs as $tx)
                                <tr>
                                    <td class="py-2 pr-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $tx->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                            {{ ucfirst($tx->type) }}
                                        </span>
                                    </td>
                                    <td class="py-2 pr-3 font-semibold {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}৳{{ number_format($tx->amount, 0) }}
                                    </td>
                                    <td class="py-2 pr-3 text-gray-500 text-xs">{{ $tx->note ?? '—' }}</td>
                                    <td class="py-2 text-xs text-gray-400">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif
</div>
