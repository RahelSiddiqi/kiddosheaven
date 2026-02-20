<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">My Addresses</h2>
        @if (!$showForm)
            <button wire:click="startCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Address
            </button>
        @endif
    </div>

    {{-- Address Form --}}
    @if ($showForm)
        <div class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                {{ $editingId ? 'Edit Address' : 'New Address' }}
            </h3>

            <form wire:submit="save" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    {{-- Full Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                        <input type="text" wire:model="name"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="Enter full name">
                        @error('name') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                        <input type="tel" wire:model="phone"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="+251 9XX XXX XXX">
                        @error('phone') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Address Line 1 --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Line 1 *</label>
                        <input type="text" wire:model="addressLine1"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="Street address, building, etc.">
                        @error('addressLine1') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Address Line 2 --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Line 2</label>
                        <input type="text" wire:model="addressLine2"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="Apartment, suite, floor (optional)">
                        @error('addressLine2') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City *</label>
                        <input type="text" wire:model="city"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="e.g. Addis Ababa">
                        @error('city') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- District/Subcity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">District/Subcity</label>
                        <input type="text" wire:model="district"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="e.g. Bole">
                        @error('district') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Postal Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                        <input type="text" wire:model="postalCode"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            placeholder="Optional">
                        @error('postalCode') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Default Checkbox --}}
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="isDefault"
                                class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as default address</span>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Address' : 'Save Address' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                    <button type="button" wire:click="cancelForm"
                        class="px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Address List --}}
    @if ($this->addresses->isEmpty() && !$showForm)
        {{-- Empty State --}}
        <div class="text-center py-8">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't added any addresses yet.</p>
            <button wire:click="startCreate"
                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Your First Address
            </button>
        </div>
    @else
        {{-- Address Cards Grid --}}
        <div class="grid md:grid-cols-2 gap-4">
            @foreach ($this->addresses as $address)
                <div class="p-4 rounded-xl border {{ $address->is_default ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-600' }} relative">
                    {{-- Default Badge --}}
                    @if ($address->is_default)
                        <span class="absolute top-3 right-3 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded">
                            Default
                        </span>
                    @endif

                    {{-- Address Details --}}
                    <div class="pr-16">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $address->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $address->phone }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ $address->address_line1 }}
                            @if ($address->address_line2)
                                <br>{{ $address->address_line2 }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $address->city }}{{ $address->district ? ', ' . $address->district : '' }}{{ $address->postal_code ? ' - ' . $address->postal_code : '' }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-600">
                        <button wire:click="startEdit({{ $address->id }})"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        @if (!$address->is_default)
                            <button wire:click="setDefault({{ $address->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Set Default
                            </button>
                        @endif
                        <button wire:click="delete({{ $address->id }})"
                            wire:confirm="Are you sure you want to delete this address?"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
