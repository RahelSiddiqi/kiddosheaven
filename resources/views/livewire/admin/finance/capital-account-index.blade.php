<div>
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Capital Accounts</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage partner and investor capital accounts</p>
        </div>
        <button wire:click="createModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Account
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Accounts</p>
                    <p class="text-lg font-bold text-gray-900">{{ $this->stats['total_accounts'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Capital</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($this->stats['total_capital'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Credited</p>
                    <p class="text-lg font-bold text-green-600">{{ number_format($this->stats['total_credited'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Debited</p>
                    <p class="text-lg font-bold text-red-600">{{ number_format($this->stats['total_debited'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search accounts..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none w-full" />
        </div>
        <select wire:model.live="typeFilter" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
            <option value="">All Types</option>
            <option value="capital_contribution">Capital Contribution</option>
            <option value="purchase_contribution">Purchase Contribution</option>
            <option value="profit_share">Profit Share</option>
            <option value="withdrawal">Withdrawal</option>
        </select>
        <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="closed">Closed</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Account</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Owner</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Balance</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide">Profit Share</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                        <tr wire:key="account-{{ $account->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">{{ $account->name }}</span>
                                <p class="text-xs text-gray-500 font-mono">{{ $account->account_number }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $account->owner?->name ?? 'N/A' }}
                                <span class="text-xs text-gray-400 block">{{ class_basename($account->owner_type ?? '') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ ucfirst(str_replace('_', ' ', $account->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono {{ $account->balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($account->balance, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $account->profit_share_percentage ? number_format($account->profit_share_percentage, 1) . '%' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <select wire:change="updateStatus({{ $account->id }}, $event.target.value)" class="text-xs border border-gray-200 rounded-lg px-2 py-1 {{ $account->status_badge_class ?? 'bg-gray-100' }}">
                                    <option value="active" @selected($account->status === 'active')>Active</option>
                                    <option value="closed" @selected($account->status === 'closed')>Closed</option>
                                    <option value="suspended" @selected($account->status === 'suspended')>Suspended</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="editModal({{ $account->id }})" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteRecord({{ $account->id }})" wire:confirm="Delete this account?" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="font-medium">No capital accounts found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $accounts->links() }}</div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                    <h3 class="font-semibold text-gray-900">{{ $editingId ? 'Edit Account' : 'New Capital Account' }}</h3>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Owner Type <span class="text-red-500">*</span></label>
                            <select wire:model.live="owner_type" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                                <option value="partner">Partner</option>
                                <option value="investor">Investor</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Owner <span class="text-red-500">*</span></label>
                            <select wire:model="owner_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                                <option value="">Select {{ ucfirst($owner_type) }}</option>
                                @if($owner_type === 'partner')
                                    @foreach($this->partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                    @endforeach
                                @else
                                    @foreach($this->investors as $investor)
                                        <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('owner_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Account Name <span class="text-red-500">*</span></label>
                            <input wire:model="name" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" placeholder="Account name" />
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Account Type <span class="text-red-500">*</span></label>
                            <select wire:model="type" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                                <option value="capital_contribution">Capital Contribution</option>
                                <option value="purchase_contribution">Purchase Contribution</option>
                                <option value="profit_share">Profit Share</option>
                                <option value="withdrawal">Withdrawal</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select wire:model="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Initial Balance <span class="text-red-500">*</span></label>
                            <input wire:model="balance" type="number" step="0.01" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" placeholder="0.00" />
                            @error('balance') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Profit Share %</label>
                            <input wire:model="profit_share_percentage" type="number" step="0.1" min="0" max="100" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" placeholder="0.0" />
                            @error('profit_share_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Expense Share %</label>
                            <input wire:model="expense_share_percentage" type="number" step="0.1" min="0" max="100" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" placeholder="0.0" />
                            @error('expense_share_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove>{{ $editingId ? 'Update' : 'Create' }}</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
