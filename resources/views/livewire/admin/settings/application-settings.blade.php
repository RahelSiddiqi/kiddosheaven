<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 lg:col-span-8">
        <form wire:submit="save"
            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">

            <!-- Header -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Application Settings</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your store settings and preferences.</p>
            </div>

            <!-- General Information -->
            <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">General Information</h4>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="site_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Site Name *</label>
                        <input type="text" wire:model="settings.site_name" id="site_name"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        @error('settings.site_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Currency *</label>
                        <select wire:model="settings.currency" id="currency"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="USD" {{ ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR (E)</option>
                            <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP (P)</option>
                            <option value="BDT" {{ ($settings['currency'] ?? '') === 'BDT' ? 'selected' : '' }}>BDT (T)</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 mt-4">
                    <div>
                        <label for="site_email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Contact Email *</label>
                        <input type="email" wire:model="settings.site_email" id="site_email"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        @error('settings.site_email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="site_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
                        <input type="text" wire:model="settings.site_phone" id="site_phone"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        @error('settings.site_phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="site_address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
                    <textarea wire:model="settings.site_address" id="site_address" rows="2"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"></textarea>
                    @error('settings.site_address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="site_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Site Description</label>
                    <textarea wire:model="settings.site_description" id="site_description" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"></textarea>
                    @error('settings.site_description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SEO Settings --}}
            <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">SEO Settings</h4>
                <div class="grid gap-4 md:grid-cols-1">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meta Description</label>
                        <textarea wire:model="settings.meta_description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="Default meta description for search engines (150-160 chars)..."></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Google Analytics ID</label>
                        <input type="text" wire:model="settings.google_analytics_id"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="G-XXXXXXXXXX">
                    </div>
                </div>
            </div>

            <!-- Commerce Settings -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Commerce Settings</h4>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="tax_rate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tax Rate (%)</label>
                        <input type="number" wire:model="settings.tax_rate" id="tax_rate" step="0.01" min="0" max="100"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        @error('settings.tax_rate')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="free_shipping_threshold" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Free Shipping Threshold ($)</label>
                        <input type="number" wire:model="settings.free_shipping_threshold" id="free_shipping_threshold" step="0.01" min="0"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        @error('settings.free_shipping_threshold')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0001 2.91659C6.21676 2.91659 2.91676 6.21659 2.91676 9.99993C2.91676 13.7833 6.21676 17.0833 10.0001 17.0833C13.7834 17.0833 17.0834 13.7833 17.0834 9.99993C17.0834 6.21659 13.7834 2.91659 10.0001 2.91659Z" stroke="currentColor" stroke-width="1.5" />
                        <path d="M12.5001 10.4167L10.0001 12.9167L7.50008 10.4167" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
