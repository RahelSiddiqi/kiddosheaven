<div>
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('admin.attributes.index') }}" wire:navigate class="hover:text-[var(--color-primary)] transition">Attributes</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
            <li class="text-gray-900 font-medium">{{ $attribute->name }} Values</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $attribute->name }} Values</h1>
            <div class="flex items-center gap-3 mt-1">
                @php
                    $typeColors = [
                        'select'      => 'bg-blue-50 text-blue-700 border-blue-200',
                        'multiselect' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'color'       => 'bg-pink-50 text-pink-700 border-pink-200',
                        'text'        => 'bg-gray-100 text-gray-700 border-gray-200',
                        'number'      => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'boolean'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'date'        => 'bg-orange-50 text-orange-700 border-orange-200',
                    ];
                    $badgeClass = $typeColors[$attribute->type] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                    {{ ucfirst($attribute->type) }}
                </span>
                <span class="text-sm text-gray-500">{{ $attribute->values_count }} {{ Str::plural('value', $attribute->values_count) }}</span>
            </div>
        </div>
        <a href="{{ route('admin.attributes.index') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Attributes
        </a>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Values table (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Value</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Display Value</th>
                                @if($attribute->type === 'color')
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide">Color</th>
                                @endif
                                <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Price Modifier</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($values as $val)
                                <tr wire:key="val-{{ $val->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $val->value }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $val->display_value ?? '—' }}</td>
                                    @if($attribute->type === 'color')
                                        <td class="px-4 py-3 text-center">
                                            @if($val->color_code)
                                                <span class="inline-block w-6 h-6 rounded-full border border-gray-300 shadow-sm" style="background-color: {{ $val->color_code }}" title="{{ $val->color_code }}"></span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 text-right">
                                        @if($val->price_modifier)
                                            <span class="{{ $val->price_modifier > 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                                                {{ $val->price_modifier > 0 ? '+' : '' }}{{ number_format($val->price_modifier, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <button wire:click="editModal({{ $val->id }})" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteValue({{ $val->id }})" wire:confirm="Delete value '{{ addslashes($val->value) }}'?" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $attribute->type === 'color' ? 5 : 4 }}" class="px-4 py-16 text-center text-gray-500">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        <p class="font-medium">No values yet</p>
                                        <p class="text-xs text-gray-400 mt-1">Add values using the form on the right</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Add Value form (1/3) --}}
        <div class="space-y-4">
            {{-- Add Value Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Add Value</h3>
                <form wire:submit="addValue" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                        <input wire:model="newValue" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" placeholder="e.g. Red, XL, Cotton" />
                        @error('newValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Display Value</label>
                        <input wire:model="newDisplayValue" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" placeholder="Optional display label" />
                    </div>

                    @if($attribute->type === 'color')
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Color Code</label>
                            <div class="flex items-center gap-2">
                                <input wire:model="newColorCode" type="color" class="w-10 h-10 rounded border border-gray-200 cursor-pointer" />
                                <input wire:model="newColorCode" type="text" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" placeholder="#000000" />
                            </div>
                        </div>
                    @endif

                    <button type="submit" wire:loading.attr="disabled" class="w-full px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="addValue">Add Value</span>
                        <span wire:loading wire:target="addValue">Adding...</span>
                    </button>
                </form>
            </div>

            {{-- Bulk Import Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Bulk Import</h3>
                <p class="text-xs text-gray-500 mb-4">Import multiple values at once from a list.</p>
                <button wire:click="openBulkModal" class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Bulk Import...
                </button>
            </div>
        </div>
    </div>

    {{-- Edit Value Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeEditModal"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Edit Value</h3>
                    <button wire:click="closeEditModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="saveEdit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                        <input wire:model="editValue" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                        @error('editValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Display Value</label>
                        <input wire:model="editDisplayValue" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                    </div>

                    @if($attribute_type === 'color')
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Color Code</label>
                            <div class="flex items-center gap-2">
                                <input wire:model="editColorCode" type="color" class="w-10 h-10 rounded border border-gray-200 cursor-pointer" />
                                <input wire:model="editColorCode" type="text" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Price Modifier</label>
                        <input wire:model="editPriceModifier" type="number" step="0.01" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" placeholder="e.g. 10.00 or -5.00" />
                        <p class="text-xs text-gray-400 mt-1">Add or subtract from base price</p>
                        @error('editPriceModifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="saveEdit">Update</span>
                            <span wire:loading wire:target="saveEdit">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Bulk Import Modal --}}
    @if($showBulkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeBulkModal"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Bulk Import Values</h3>
                    <button wire:click="closeBulkModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="bulkImport" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Values (one per line)</label>
                        <textarea wire:model="bulkText" rows="10" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none resize-none" placeholder="red|Red Color
blue|Blue Color
green|Green Color

Use pipe (|) to separate value from display label"></textarea>
                        @error('bulkText') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600">
                        <p class="font-semibold mb-1">Format:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Enter one value per line</li>
                            <li>Use <code class="bg-gray-200 px-1 rounded">|</code> to separate value from display label</li>
                            <li>Example: <code class="bg-gray-200 px-1 rounded">sm|Small</code></li>
                            <li>Duplicates are skipped automatically</li>
                            <li>Maximum 500 values per import</li>
                        </ul>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeBulkModal" class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="bulkImport">Import</span>
                            <span wire:loading wire:target="bulkImport">Importing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
