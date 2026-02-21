<div>
    <div class="flex gap-6 min-h-[600px]">
        {{-- Column 1: Customer Groups --}}
        <div class="w-64 flex-shrink-0">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Customer Groups</h4>
                    <button wire:click="openCreateGroup" class="text-blue-600 hover:text-blue-700">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($groups as $g)
                        <div wire:click="selectGroup({{ $g->id }})"
                             class="flex items-center justify-between px-4 py-3 cursor-pointer transition-colors
                                    {{ $activeGroupId === $g->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-white/[0.02]' }}">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $g->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $g->customers_count }} customers
                                    @if ($g->discount_percent > 0)· {{ $g->discount_percent }}% off@endif
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                @if ($g->is_default)
                                    <span class="text-[10px] font-semibold text-blue-500 uppercase">DEFAULT</span>
                                @endif
                                <button wire:click.stop="openEditGroup({{ $g->id }})" class="text-gray-300 hover:text-gray-500">
                                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-xs text-center text-gray-400">No groups yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Column 2: Price Lists for selected group --}}
        <div class="w-64 flex-shrink-0">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $activeGroup ? $activeGroup->name . ' Lists' : 'Price Lists' }}
                    </h4>
                    @if ($activeGroupId)
                        <button wire:click="openCreateList({{ $activeGroupId }})" class="text-blue-600 hover:text-blue-700">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    @endif
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @if (! $activeGroupId)
                        <p class="px-4 py-6 text-xs text-center text-gray-400">Select a group first.</p>
                    @elseif ($lists->isEmpty())
                        <p class="px-4 py-6 text-xs text-center text-gray-400">No price lists.</p>
                    @else
                        @foreach ($lists as $list)
                            <div wire:click="selectList({{ $list->id }})"
                                 class="flex items-center justify-between px-4 py-3 cursor-pointer transition-colors
                                        {{ $activeListId === $list->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-white/[0.02]' }}">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $list->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $list->items_count }} items</p>
                                </div>
                                <span class="h-2 w-2 rounded-full {{ $list->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Column 3: Price Items --}}
        <div class="flex-1">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $activeList ? $activeList->name . ' — Product Prices' : 'Product Prices' }}
                    </h4>
                    @if ($activeListId)
                        <button wire:click="openAddItem({{ $activeListId }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">
                            + Add Price
                        </button>
                    @endif
                </div>
                @if (! $activeListId)
                    <p class="px-4 py-12 text-sm text-center text-gray-400">Select a price list to manage item prices.</p>
                @elseif ($items->isEmpty())
                    <p class="px-4 py-12 text-sm text-center text-gray-400">No price overrides yet. Click "+ Add Price" to start.</p>
                @else
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">SKU</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Retail</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">B2B Price</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Min Qty</th>
                                <th class="relative px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200 max-w-[180px] truncate">
                                        {{ $item->product->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-400 font-mono">{{ $item->product->sku ?? '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">৳{{ number_format($item->product->price ?? 0, 0) }}</td>
                                    <td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">৳{{ number_format($item->price, 0) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $item->min_qty }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Remove this price override?" class="text-xs text-red-500 hover:underline">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Group Form --}}
    @if ($showGroupForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showGroupForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingGroupId ? 'Edit Group' : 'New Customer Group' }}</h3>
                    <button wire:click="$set('showGroupForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Group Name *</label>
                        <input wire:model="group_name" type="text" placeholder="Wholesale"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        @error('group_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Blanket Discount %</label>
                            <input wire:model="group_discount" type="number" step="0.01" min="0" max="100" placeholder="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min Order Amount</label>
                            <input wire:model="group_min_order" type="number" step="0.01" min="0" placeholder="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="group_is_default" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Default Group
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="group_tax_exempt" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Tax Exempt
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showGroupForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveGroup" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Group</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Price List Form --}}
    @if ($showListForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showListForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Price List</h3>
                    <button wire:click="$set('showListForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">List Name *</label>
                        <input wire:model="list_name" type="text" placeholder="Q1 2026 Wholesale"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        @error('list_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input wire:model="list_starts_at" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input wire:model="list_ends_at" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input wire:model="list_is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Active
                    </label>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showListForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveList" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Item Form --}}
    @if ($showItemForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showItemForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Add Price Override</h3>
                    <button wire:click="$set('showItemForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Product *</label>
                        <select wire:model="item_product_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            <option value="">Select a product...</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} @if($p->sku) ({{ $p->sku }}) @endif — ৳{{ number_format($p->price, 0) }}</option>
                            @endforeach
                        </select>
                        @error('item_product_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">B2B Price *</label>
                            <input wire:model="item_price" type="number" step="0.01" min="0" placeholder="0.00"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            @error('item_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min Qty</label>
                            <input wire:model="item_min_qty" type="number" min="1" placeholder="1"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showItemForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveItem" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Add Price</button>
                </div>
            </div>
        </div>
    @endif
</div>
