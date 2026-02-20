<div>
    <x-admin.ui.entity-header
        title="Theme Settings"
        subtitle="Customize your storefront appearance"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Settings', 'url' => route('admin.settings.edit')],
            ['label' => 'Theme']
        ]"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Settings Form --}}
        <div class="lg:col-span-2">
            <form wire:submit="save" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Theme Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure colors, fonts, and theme style</p>
                </div>

                <div class="p-5 space-y-6">
                    {{-- Active Theme --}}
                    <div>
                        <label for="activeTheme" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Active Theme</label>
                        <select
                            id="activeTheme"
                            wire:model.live="activeTheme"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                        >
                            @foreach($themes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Colors Section --}}
                    <div class="space-y-4">
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Colors</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- Primary Color --}}
                            <div>
                                <label for="primaryColor" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Primary Color</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="color"
                                        id="primaryColor"
                                        wire:model.live="primaryColor"
                                        class="h-11 w-14 rounded-lg border border-gray-300 cursor-pointer dark:border-gray-700"
                                    />
                                    <input
                                        type="text"
                                        wire:model.live="primaryColor"
                                        class="h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                        placeholder="#3b82f6"
                                    />
                                </div>
                            </div>

                            {{-- Secondary Color --}}
                            <div>
                                <label for="secondaryColor" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Secondary Color</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="color"
                                        id="secondaryColor"
                                        wire:model.live="secondaryColor"
                                        class="h-11 w-14 rounded-lg border border-gray-300 cursor-pointer dark:border-gray-700"
                                    />
                                    <input
                                        type="text"
                                        wire:model.live="secondaryColor"
                                        class="h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                        placeholder="#6b7280"
                                    />
                                </div>
                            </div>

                            {{-- Accent Color --}}
                            <div>
                                <label for="accentColor" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Accent Color</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="color"
                                        id="accentColor"
                                        wire:model.live="accentColor"
                                        class="h-11 w-14 rounded-lg border border-gray-300 cursor-pointer dark:border-gray-700"
                                    />
                                    <input
                                        type="text"
                                        wire:model.live="accentColor"
                                        class="h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                        placeholder="#f59e0b"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Typography Section --}}
                    <div>
                        <label for="fontFamily" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Font Family</label>
                        <select
                            id="fontFamily"
                            wire:model.live="fontFamily"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                        >
                            @foreach($fonts as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="h-11 inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="save">Save Settings</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Preview --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] sticky top-6">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Live Preview</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">See your changes in real-time</p>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Sample Button --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Primary Button</p>
                        <button
                            type="button"
                            class="h-10 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-theme-xs transition-colors"
                            style="background-color: {{ $primaryColor }}; font-family: {{ $fontFamily }}, sans-serif;"
                        >
                            Add to Cart
                        </button>
                    </div>

                    {{-- Sample Secondary Button --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Secondary Button</p>
                        <button
                            type="button"
                            class="h-10 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium border shadow-theme-xs transition-colors"
                            style="border-color: {{ $secondaryColor }}; color: {{ $secondaryColor }}; font-family: {{ $fontFamily }}, sans-serif;"
                        >
                            View Details
                        </button>
                    </div>

                    {{-- Sample Accent Element --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Accent Badge</p>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white"
                            style="background-color: {{ $accentColor }}; font-family: {{ $fontFamily }}, sans-serif;"
                        >
                            Sale -20%
                        </span>
                    </div>

                    {{-- Sample Text --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Typography Sample</p>
                        <div
                            class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                            style="font-family: {{ $fontFamily }}, sans-serif;"
                        >
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-1">Product Title</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">This is how your product descriptions will appear on the storefront.</p>
                            <p class="text-lg font-bold mt-2" style="color: {{ $primaryColor }};">$99.00</p>
                        </div>
                    </div>

                    {{-- Color Swatches --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Color Palette</p>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700"
                                style="background-color: {{ $primaryColor }};"
                                title="Primary"
                            ></div>
                            <div
                                class="w-10 h-10 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700"
                                style="background-color: {{ $secondaryColor }};"
                                title="Secondary"
                            ></div>
                            <div
                                class="w-10 h-10 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700"
                                style="background-color: {{ $accentColor }};"
                                title="Accent"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
