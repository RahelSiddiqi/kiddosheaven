<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.coupons.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $couponId ? 'Edit Coupon' : 'Create Coupon' }}</h1>
            <p class="text-sm text-gray-500">{{ $couponId ? 'Update coupon details' : 'Add a new discount coupon' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Coupon Identity --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Coupon Identity</h2>
            <div class="grid grid-cols-2 gap-4">

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input wire:model="code" type="text" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                        <button type="button" wire:click="generateCode" class="px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-600 hover:bg-gray-50 transition whitespace-nowrap">
                            Generate
                        </button>
                    </div>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="2" placeholder="Internal note about this coupon…" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isActive" id="coupon-active" class="rounded accent-[var(--color-primary)]" />
                    <label for="coupon-active" class="text-sm text-gray-700">Active</label>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isUserSpecific" id="coupon-user" class="rounded accent-[var(--color-primary)]" />
                    <label for="coupon-user" class="text-sm text-gray-700">User-specific coupon</label>
                </div>
            </div>
        </div>

        {{-- Discount Settings --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Discount</h2>
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Discount Type <span class="text-red-500">*</span></label>
                    <select wire:model="discountType" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (৳)</option>
                    </select>
                    @error('discountType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Discount Value
                        <span class="text-gray-400 font-normal">({{ $discountType === 'percentage' ? '%' : '৳' }})</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="discountValue" type="number" min="0" step="0.01" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                    @error('discountValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Minimum Order <span class="text-gray-400 font-normal">(৳)</span></label>
                    <input wire:model="minimumOrderAmount" type="number" min="0" step="0.01" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Max Discount <span class="text-gray-400 font-normal">(৳, optional)</span></label>
                    <input wire:model="maximumDiscountAmount" type="number" min="0" step="0.01" placeholder="No limit" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>
            </div>
        </div>

        {{-- Usage Limits --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Usage Limits & Dates</h2>
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Total Usage Limit <span class="text-gray-400 font-normal">(blank = unlimited)</span></label>
                    <input wire:model="usageLimit" type="number" min="1" placeholder="Unlimited" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Limit Per User</label>
                    <input wire:model="usageLimitPerUser" type="number" min="1" placeholder="Unlimited" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Start Date</label>
                    <input wire:model="startDate" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">End Date</label>
                    <input wire:model="endDate" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                    @error('endDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Product/Category Restrictions --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-1 uppercase tracking-wide">Restrictions</h2>
            <p class="text-xs text-gray-500 mb-4">Leave blank to apply to all products. Selecting categories also matches their products.</p>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Specific Products</label>
                    <div class="h-40 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                        @foreach($availableProducts as $product)
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" wire:model="selectedProducts" value="{{ $product['id'] }}" class="rounded accent-[var(--color-primary)]" />
                                <span class="text-xs text-gray-700 truncate">{{ $product['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ count($selectedProducts) }} selected</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Specific Categories</label>
                    <div class="h-40 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                        @foreach($availableCategories as $cat)
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" wire:model="selectedCategories" value="{{ $cat['id'] }}" class="rounded accent-[var(--color-primary)]" />
                                <span class="text-xs text-gray-700 truncate">{{ $cat['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ count($selectedCategories) }} selected</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pb-8">
            <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                <span wire:loading.remove>{{ $couponId ? 'Update Coupon' : 'Create Coupon' }}</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>

    </form>
</div>
