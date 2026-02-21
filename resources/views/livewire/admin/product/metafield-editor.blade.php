<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Metafields
            <span class="ml-2 text-xs font-normal text-gray-400">{{ strtolower($ownerType) }} #{{ $ownerId }}</span>
        </h4>
        <button wire:click="openCreate"
            class="inline-flex items-center gap-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-3 py-1.5 text-gray-700 dark:text-gray-300 transition-colors font-medium">
            + Add
        </button>
    </div>

    @if ($metas->isEmpty())
        <p class="text-sm text-gray-400 text-center py-4">No metafields yet.</p>
    @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <th class="py-2 text-left text-xs font-medium text-gray-500">Namespace</th>
                    <th class="py-2 text-left text-xs font-medium text-gray-500">Key</th>
                    <th class="py-2 text-left text-xs font-medium text-gray-500">Value</th>
                    <th class="py-2 text-left text-xs font-medium text-gray-500">Type</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach ($metas as $meta)
                    <tr>
                        <td class="py-2 pr-3 font-mono text-xs text-gray-500">{{ $meta->namespace }}</td>
                        <td class="py-2 pr-3 font-mono text-xs text-gray-900 dark:text-white font-medium">{{ $meta->key }}</td>
                        <td class="py-2 pr-3 text-xs text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $meta->value }}</td>
                        <td class="py-2 pr-3">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $meta->value_type }}
                            </span>
                        </td>
                        <td class="py-2 text-right whitespace-nowrap">
                            <button wire:click="openEdit({{ $meta->id }})" class="text-xs text-blue-500 hover:underline mr-3">Edit</button>
                            <button wire:click="deleteMeta({{ $meta->id }})" wire:confirm="Delete this metafield?"
                                class="text-xs text-red-400 hover:underline">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Form Modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $editingId ? 'Edit Metafield' : 'Add Metafield' }}
                </h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Namespace</label>
                            <input wire:model="namespace" type="text" placeholder="custom"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Key</label>
                            <input wire:model="key" type="text" placeholder="e.g. material"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                        <textarea wire:model="value" rows="3" placeholder="Value…"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select wire:model="value_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            <option value="string">string</option>
                            <option value="integer">integer</option>
                            <option value="json">json</option>
                            <option value="boolean">boolean</option>
                        </select>
                    </div>
                </div>

                @error('namespace') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('key')       <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('value')     <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                <div class="flex justify-end gap-3 mt-5">
                    <button wire:click="$set('showForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="setMeta" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>
