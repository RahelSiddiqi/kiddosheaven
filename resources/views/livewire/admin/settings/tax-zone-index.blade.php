<div>
    <div class="space-y-4">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tax Zones</h2>
                <p class="text-sm text-gray-500 mt-0.5">Configure tax rates by country or region</p>
            </div>
            <button wire:click="openCreateZone"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4.167v11.666M4.167 10h11.666" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Add Zone
            </button>
        </div>

        <!-- Zones -->
        @forelse ($zones as $zone)
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/50 overflow-hidden">
                <!-- Zone Header -->
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $zone->name }}</h4>
                                @if ($zone->is_default)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Default</span>
                                @endif
                                @if (! $zone->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </div>
                            @if ($zone->countries)
                                <p class="text-xs text-gray-400 mt-0.5">{{ implode(', ', $zone->countries) }}</p>
                            @else
                                <p class="text-xs text-gray-400 mt-0.5">All countries</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="openAddRate({{ $zone->id }})"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            + Add Rate
                        </button>
                        <button wire:click="openEditZone({{ $zone->id }})"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            Edit
                        </button>
                        <button wire:click="deleteZone({{ $zone->id }})" wire:confirm="Delete this tax zone and all its rates?"
                            class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:bg-transparent dark:text-red-400">
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Rates table -->
                @if ($zone->rates->isNotEmpty())
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                                <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Rate</th>
                                <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Class</th>
                                <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Applies To</th>
                                <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Compound</th>
                                <th class="relative px-5 py-2"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($zone->rates as $rate)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $rate->name }}</td>
                                    <td class="px-5 py-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $rate->rate }}%</td>
                                    <td class="px-5 py-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium
                                            {{ $rate->tax_class === 'standard' ? 'bg-blue-50 text-blue-700' :
                                               ($rate->tax_class === 'zero' ? 'bg-green-50 text-green-700' :
                                               ($rate->tax_class === 'exempt' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-50 text-yellow-700')) }}">
                                            {{ ucfirst($rate->tax_class) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-xs text-gray-500">{{ ucfirst($rate->applies_to) }}</td>
                                    <td class="px-5 py-2 text-xs text-gray-500">{{ $rate->is_compound ? 'Yes' : 'No' }}</td>
                                    <td class="px-5 py-2 text-right">
                                        <button wire:click="openEditRate({{ $rate->id }})" class="text-xs text-blue-600 hover:underline mr-3">Edit</button>
                                        <button wire:click="deleteRate({{ $rate->id }})" wire:confirm="Delete this rate?" class="text-xs text-red-500 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-5 py-4 text-xs text-gray-400">No rates yet. Click "+ Add Rate" to add.</p>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 py-16 text-center">
                <p class="text-sm text-gray-400">No tax zones configured.</p>
                <button wire:click="openCreateZone" class="mt-2 text-sm text-blue-600 hover:underline">Create your first zone</button>
            </div>
        @endforelse
    </div>

    {{-- Zone Form Modal --}}
    @if ($showZoneForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showZoneForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingZoneId ? 'Edit Zone' : 'New Tax Zone' }}</h3>
                    <button wire:click="$set('showZoneForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Zone Name *</label>
                        <input wire:model="zone_name" type="text" placeholder="Bangladesh"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('zone_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Countries <span class="text-gray-400">(ISO codes, comma-separated. Leave empty = all)</span>
                        </label>
                        <input wire:model="zone_countries" type="text" placeholder="BD, IN, US"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="zone_is_default" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Default Zone
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="zone_is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Active
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showZoneForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveZone" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Zone</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Rate Form Modal --}}
    @if ($showRateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="$set('showRateForm', false)">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingRateId ? 'Edit Rate' : 'Add Tax Rate' }}</h3>
                    <button wire:click="$set('showRateForm', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rate Name *</label>
                            <input wire:model="rate_name" type="text" placeholder="VAT"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                            @error('rate_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rate % *</label>
                            <input wire:model="rate_rate" type="number" step="0.01" min="0" max="100" placeholder="15"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Class</label>
                            <select wire:model="rate_tax_class" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                <option value="standard">Standard</option>
                                <option value="reduced">Reduced</option>
                                <option value="zero">Zero Rated</option>
                                <option value="exempt">Exempt</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Applies To</label>
                            <select wire:model="rate_applies_to" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                <option value="all">All</option>
                                <option value="physical">Physical</option>
                                <option value="digital">Digital</option>
                                <option value="shipping">Shipping</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="rate_is_compound" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                            Compound <span class="text-gray-400 text-xs">(applied after other taxes)</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input wire:model="rate_is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600"> Active
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="$set('showRateForm', false)" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveRate" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Rate</button>
                </div>
            </div>
        </div>
    @endif
</div>
