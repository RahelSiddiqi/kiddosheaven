@extends('admin.layouts.app')

@section('title', 'Edit User — Kiddo\'s Heaven')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 xl:col-span-8 xl:col-start-3">

        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-brand-500">Users</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-white/90">{{ $user->name }}</span>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-500 text-lg font-semibold text-white uppercase">
                    {{ mb_substr($user->name, 0, 1) }}
                </span>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="px-6 py-6 space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Name -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 no-select2">
                            <option value="">— No Role —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ $role->name }}
                                    @if ($role->description) — {{ Str::limit($role->description, 40) }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>

                <!-- Toggles -->
                <div class="flex flex-wrap gap-6 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_admin" value="1" class="sr-only peer" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
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
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <div class="block h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-brand-500 dark:bg-gray-700 transition-colors"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>

                <!-- Role Permissions Preview -->
                @if ($user->role && $user->role->permissions->isNotEmpty())
                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-800 dark:bg-blue-900/20">
                        <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-2 uppercase tracking-wide">Permissions via "{{ $user->role->name }}" role</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($user->role->permissions as $perm)
                                <span class="rounded-full bg-white border border-blue-200 px-2.5 py-0.5 text-xs text-blue-700 dark:bg-blue-900/40 dark:border-blue-700 dark:text-blue-300">
                                    {{ $perm->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @elseif ($user->is_admin)
                    <div class="rounded-lg border border-purple-200 bg-purple-50 px-4 py-3 dark:border-purple-800 dark:bg-purple-900/20">
                        <p class="text-xs font-semibold text-purple-700 dark:text-purple-400">Super Admin — has access to everything</p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-400">
                        Created {{ $user->created_at->format('d M Y') }}
                        · Last updated {{ $user->updated_at->diffForHumans() }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
