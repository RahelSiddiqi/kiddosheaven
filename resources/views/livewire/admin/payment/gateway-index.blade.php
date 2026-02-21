<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Payment Gateways</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enable and configure payment methods for your store</p>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Add Gateway
            </button>
        </div>

        <!-- Gateway cards -->
        <div class="px-5 pb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:px-6">
            @forelse ($gateways as $gw)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            @if ($gw->icon)
                                <img src="{{ $gw->icon }}" alt="{{ $gw->name }}" class="h-8 w-8 rounded object-contain">
                            @else
                                <div class="h-8 w-8 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="text-gray-400" width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M2 10h20" stroke="currentColor" stroke-width="2"/></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $gw->name }}</p>
                                <code class="text-xs text-gray-400 font-mono">{{ $gw->code }}</code>
                            </div>
                        </div>
                        <!-- Toggle -->
                        <button wire:click="toggle({{ $gw->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $gw->is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                            <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform {{ $gw->is_active ? 'translate-x-4.5' : 'translate-x-0.5' }}"></span>
                        </button>
                    </div>
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        @if ($gw->is_test_mode)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Test Mode</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Live</span>
                        @endif
                        @if ($gw->description)
                            <span class="text-xs text-gray-400 truncate">{{ $gw->description }}</span>
                        @endif
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button wire:click="openEdit({{ $gw->id }})"
                            class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Configure
                        </button>
                        <button wire:click="delete({{ $gw->id }})" wire:confirm="Delete this gateway?"
                            class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:bg-transparent dark:text-red-400">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-12 text-center text-gray-400">
                    <svg class="mx-auto mb-3 text-gray-300" width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2 10h20" stroke="currentColor" stroke-width="1.5"/></svg>
                    <p class="text-sm">No payment gateways configured yet.</p>
                    <button wire:click="openCreate" class="mt-2 text-sm text-blue-600 hover:underline">Add your first gateway</button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Slide-over / Modal Form -->
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Gateway' : 'Add Gateway' }}
                    </h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                            <input wire:model="name" type="text" placeholder="bKash" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Code * <span class="text-gray-400">(e.g. bkash)</span></label>
                            <input wire:model="code" type="text" placeholder="bkash" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            @error('code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Icon URL</label>
                        <input wire:model="icon" type="text" placeholder="https://..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <input wire:model="description" type="text" placeholder="Pay with bKash" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <div class="flex flex-col justify-end gap-2 pb-1">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                                Active
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <input wire:model="is_test_mode" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-yellow-500">
                                Test Mode
                            </label>
                        </div>
                    </div>
                    <!-- Config JSON -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Configuration (JSON) <span class="text-gray-400">— API keys, merchant IDs etc.</span>
                        </label>
                        <textarea wire:model="configJson" rows="6"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            placeholder='{"app_key": "...", "app_secret": "..."}'></textarea>
                        @error('configJson') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">Values are stored encrypted at rest.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showForm', false)"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button wire:click="save"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
