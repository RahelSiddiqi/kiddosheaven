<div class="max-w-4xl mx-auto" x-data>
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.flash-sales.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $flashSaleId ? 'Edit Flash Sale' : 'Create Flash Sale' }}</h1>
            <p class="text-sm text-gray-500">Set up a time-limited promotional sale</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Flash Sale Details</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" placeholder="e.g. Eid Flash Sale" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Subtitle</label>
                    <input wire:model="subtitle" type="text" placeholder="Short tagline…" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Starts At <span class="text-red-500">*</span></label>
                    <input wire:model="startsAt" type="datetime-local" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                    @error('startsAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ends At <span class="text-red-500">*</span></label>
                    <input wire:model="endsAt" type="datetime-local" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                    @error('endsAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select wire:model="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center gap-4 pt-5">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-700">Background</label>
                        <input wire:model="backgroundColor" type="color" class="w-8 h-8 rounded cursor-pointer border border-gray-200" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-700">Text</label>
                        <input wire:model="textColor" type="color" class="w-8 h-8 rounded cursor-pointer border border-gray-200" />
                    </div>
                    {{-- Preview --}}
                    <div class="flex-1 rounded-lg px-3 py-1.5 text-xs font-bold text-center" style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}">
                        {{ $title ?: 'Preview' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Products <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-500">Add products and set their sale prices</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">{{ count($flashProducts) }} products</span>
            </div>

            @error('flashProducts') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

            {{-- Product search --}}
            <div class="relative mb-4" x-data="{ open: false }" x-on:click.outside="open = false">
                <div class="flex gap-2">
                    <input
                        wire:model.live.debounce.300ms="productSearch"
                        type="text"
                        placeholder="Search to add a product…"
                        class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                        x-on:focus="open = true"
                    />
                </div>

                @if($productSearch && count($filteredProducts) > 0)
                    <div class="absolute z-20 left-0 right-0 top-full mt-1 bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden divide-y divide-gray-100">
                        @foreach($filteredProducts as $p)
                            <button type="button" wire:click="addProduct({{ $p['id'] }})" class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 text-sm transition">
                                <span class="font-medium text-gray-800 truncate">{{ $p['name'] }}</span>
                                <span class="text-xs text-gray-500 ml-3">৳{{ number_format($p['price'], 2) }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Added Products --}}
            @if(count($flashProducts) > 0)
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Original</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Sale Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Limit/Customer</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($flashProducts as $idx => $fp)
                                <tr wire:key="fp-{{ $idx }}">
                                    <td class="px-4 py-3 font-medium text-gray-800 max-w-[160px] truncate">{{ $fp['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">৳{{ number_format($fp['price'], 2) }}</td>
                                    <td class="px-4 py-3">
                                        <input wire:model="flashProducts.{{ $idx }}.discount_price" type="number" min="0" step="0.01" class="w-24 rounded-lg border border-gray-200 px-2 py-1 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <select wire:model="flashProducts.{{ $idx }}.discount_type" class="rounded-lg border border-gray-200 px-2 py-1 text-xs focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                                            <option value="fixed">৳ Fixed</option>
                                            <option value="percentage">% Off</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input wire:model="flashProducts.{{ $idx }}.limit_per_customer" type="number" min="1" placeholder="∞" class="w-16 rounded-lg border border-gray-200 px-2 py-1 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <button type="button" wire:click="removeProduct({{ $idx }})" class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-xl border-2 border-dashed border-gray-200 py-10 text-center text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    <p class="text-sm">Search above to add products to this flash sale</p>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pb-8">
            <a href="{{ route('admin.flash-sales.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                <span wire:loading.remove>{{ $flashSaleId ? 'Update Flash Sale' : 'Create Flash Sale' }}</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>
    </form>
</div>
