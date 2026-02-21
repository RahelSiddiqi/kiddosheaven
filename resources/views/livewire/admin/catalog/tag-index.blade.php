<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tags</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organize products, orders and customers with tags</p>
            </div>
            <div class="flex gap-3">
                <!-- Type filter -->
                <select wire:model.live="typeFilter" class="h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none">
                    <option value="">All Types</option>
                    <option value="product">Product</option>
                    <option value="order">Order</option>
                    <option value="customer">Customer</option>
                    <option value="collection">Collection</option>
                    <option value="general">General</option>
                </select>
                <!-- Search -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 fill-gray-400" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.04 9.374A6.333 6.333 0 1115.71 9.374 6.333 6.333 0 013.04 9.374z" clip-rule="evenodd"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tags..."
                        class="h-10 rounded-lg border border-gray-300 bg-transparent pl-9 pr-4 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    New Tag
                </button>
            </div>
        </div>

        <!-- Tags grid -->
        <div class="px-5 pb-5 sm:px-6">
            @if ($tags->count())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach ($tags as $tag)
                        <div class="group inline-flex items-center gap-2 rounded-full pl-3 pr-2 py-1.5 text-sm font-medium border"
                             style="border-color: {{ $tag->color }}33; background-color: {{ $tag->color }}15; color: {{ $tag->color }};">
                            <span>{{ $tag->name }}</span>
                            <span class="text-xs opacity-60">
                                @if ($tag->type === 'product') {{ $tag->products_count ?? 0 }} products
                                @elseif ($tag->type === 'order') {{ $tag->orders_count ?? 0 }} orders
                                @endif
                            </span>
                            <span class="opacity-40 text-[10px] font-normal uppercase tracking-wide">{{ $tag->type }}</span>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="openEdit({{ $tag->id }})" class="hover:opacity-70 transition-opacity" title="Edit">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                                <button wire:click="delete({{ $tag->id }})" wire:confirm="Delete tag '{{ $tag->name }}'?" class="hover:opacity-70" title="Delete">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{ $tags->links() }}
            @else
                <div class="py-12 text-center text-gray-400">
                    <p class="text-sm">No tags found.</p>
                    <button wire:click="openCreate" class="mt-2 text-sm text-blue-600 hover:underline">Create your first tag</button>
                </div>
            @endif
        </div>
    </div>

    {{-- Tag Form --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Edit Tag' : 'New Tag' }}</h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tag Name *</label>
                        <input wire:model="name" type="text" placeholder="e.g. Featured"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select wire:model="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                <option value="product">Product</option>
                                <option value="order">Order</option>
                                <option value="customer">Customer</option>
                                <option value="collection">Collection</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                            <div class="flex items-center gap-2">
                                <input wire:model="color" type="color" class="h-9 w-9 cursor-pointer rounded border border-gray-300 dark:border-gray-600 p-0.5">
                                <input wire:model="color" type="text" placeholder="#6366f1"
                                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                    <!-- Preview -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Preview:</span>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                              style="background-color: {{ $color ?? '#6366f1' }}20; color: {{ $color ?? '#6366f1' }}; border: 1px solid {{ $color ?? '#6366f1' }}40;">
                            {{ $name ?: 'Tag Name' }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="save" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $editingId ? 'Update' : 'Create' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
