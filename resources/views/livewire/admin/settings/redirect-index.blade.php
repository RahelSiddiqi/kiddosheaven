<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">URL Redirects</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage 301/302 redirects for SEO and site restructuring</p>
            </div>
            <div class="flex gap-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 fill-gray-400" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.04 9.374A6.333 6.333 0 1115.71 9.374 6.333 6.333 0 013.04 9.374zm13.43 5.888l-2.82-2.82a7.833 7.833 0 10-1.414 1.414l2.82 2.82a1 1 0 001.414-1.414z" clip-rule="evenodd"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search paths..."
                        class="h-10 rounded-lg border border-gray-300 bg-transparent pl-9 pr-4 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Add Redirect
                </button>
            </div>
        </div>

        <div class="overflow-hidden">
            <div class="max-w-full px-5 overflow-x-auto sm:px-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-200 border-y dark:border-gray-700">
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">From</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">To</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Type</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Hits</th>
                            <th class="px-4 py-3 font-normal text-gray-500 text-start text-xs dark:text-gray-400">Active</th>
                            <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($redirects as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-3 max-w-[220px] truncate">
                                    <code class="text-xs font-mono text-gray-800 dark:text-gray-300">{{ $r->from_path }}</code>
                                </td>
                                <td class="px-4 py-3 max-w-[220px] truncate">
                                    <code class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $r->to_path }}</code>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-xs font-mono font-semibold {{ $r->type === 301 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                                        {{ $r->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ number_format($r->hit_count) }}</td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggle({{ $r->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $r->is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform {{ $r->is_active ? 'translate-x-4.5' : 'translate-x-0.5' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <button wire:click="openEdit({{ $r->id }})" class="text-xs text-blue-600 hover:underline mr-3">Edit</button>
                                    <button wire:click="delete({{ $r->id }})" wire:confirm="Delete this redirect?" class="text-xs text-red-500 hover:underline">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-sm text-gray-400">No redirects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($redirects->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $redirects->links() }}
            </div>
        @endif
    </div>

    <!-- Form Modal -->
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Edit Redirect' : 'New Redirect' }}</h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From Path *</label>
                        <input wire:model="from_path" type="text" placeholder="/old-page"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('from_path') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To Path / URL *</label>
                        <input wire:model="to_path" type="text" placeholder="/new-page or https://..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('to_path') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Redirect Type</label>
                            <select wire:model="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                <option value="301">301 — Permanent</option>
                                <option value="302">302 — Temporary</option>
                            </select>
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Active
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Note (optional)</label>
                        <input wire:model="note" type="text" placeholder="Reason for redirect..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="save" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $editingId ? 'Update' : 'Create' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
