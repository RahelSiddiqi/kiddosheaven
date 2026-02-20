<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">My Profile</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your account settings</p>
    </div>

    {{-- Tabs --}}
    <div class="mb-6">
        <nav class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
            <button
                wire:click="$set('activeTab', 'profile')"
                @class([
                    'pb-3 px-1 text-sm font-medium border-b-2 transition',
                    'border-brand-500 text-brand-600 dark:text-brand-400' => $activeTab === 'profile',
                    'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'profile',
                ])
            >
                Profile Information
            </button>
            <button
                wire:click="$set('activeTab', 'password')"
                @class([
                    'pb-3 px-1 text-sm font-medium border-b-2 transition',
                    'border-brand-500 text-brand-600 dark:text-brand-400' => $activeTab === 'password',
                    'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'password',
                ])
            >
                Change Password
            </button>
        </nav>
    </div>

    {{-- Profile Information Tab --}}
    @if($activeTab === 'profile')
        <div class="bg-white dark:bg-white/[0.03] rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Profile Information</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Update your account profile information.</p>
            </div>
            <div class="p-6 space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                    <input
                        type="text"
                        id="phone"
                        wire:model="phone"
                        placeholder="Optional"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl">
                <button
                    wire:click="updateProfile"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="updateProfile">Save Profile</span>
                    <span wire:loading wire:target="updateProfile">Saving...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Change Password Tab --}}
    @if($activeTab === 'password')
        <div class="bg-white dark:bg-white/[0.03] rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Change Password</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Ensure your account is using a secure password.</p>
            </div>
            <div class="p-6 space-y-5">
                {{-- Current Password --}}
                <div>
                    <label for="currentPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                    <input
                        type="password"
                        id="currentPassword"
                        wire:model="currentPassword"
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('currentPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="newPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                    <input
                        type="password"
                        id="newPassword"
                        wire:model="newPassword"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('newPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="newPasswordConfirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                    <input
                        type="password"
                        id="newPasswordConfirmation"
                        wire:model="newPasswordConfirmation"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:bg-gray-900 dark:focus:border-brand-800"
                    />
                    @error('newPasswordConfirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl">
                <button
                    wire:click="updatePassword"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                    <span wire:loading wire:target="updatePassword">Updating...</span>
                </button>
            </div>
        </div>
    @endif
</div>
