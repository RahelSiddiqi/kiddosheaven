@extends('admin.layouts.app')

@section('title', 'Add User — Kiddo\'s Heaven')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 xl:col-span-8 xl:col-start-3">

        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-brand-500">Users</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-white/90">Add User</span>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add New User</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create an admin or staff account and assign a role</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="px-6 py-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Name -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 no-select2">
                            <option value="">— No Role —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                    {{ $role->name }}
                                    @if ($role->description) — {{ Str::limit($role->description, 40) }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>

                <!-- Toggles -->
                <div class="flex flex-wrap gap-6 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_admin" value="1" class="sr-only peer" {{ old('is_admin') ? 'checked' : '' }}>
                            <div class="block h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-brand-500 dark:bg-gray-700 transition-colors"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Super Admin</span>
                            <p class="text-xs text-gray-400">Bypasses all permission checks</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="block h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-brand-500 dark:bg-gray-700 transition-colors"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
