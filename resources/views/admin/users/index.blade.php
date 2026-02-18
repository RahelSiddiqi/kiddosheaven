@extends('admin.layouts.app')

@section('title', 'Users — Kiddo\'s Heaven')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12">

        @if (session('success'))
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
            <!-- Header -->
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Users</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage admin users and their roles</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="px-5 sm:px-6 mb-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
                            class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 w-60" />
                    </div>
                    <div>
                        <select name="role" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 no-select2">
                            <option value="">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(request('role') == $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 no-select2">
                            <option value="">All Status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <select name="type" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 no-select2">
                            <option value="">All Types</option>
                            <option value="admin" @selected(request('type') === 'admin')>Super Admin</option>
                            <option value="staff" @selected(request('type') === 'staff')>Staff</option>
                        </select>
                    </div>
                    <button type="submit" class="h-10 inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600">
                        Search
                    </button>
                    @if (request()->hasAny(['search','role','status','type']))
                        <a href="{{ route('admin.users.index') }}" class="h-10 inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-t border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400 sm:px-6">Name</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Role</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Joined</th>
                            <th class="px-5 py-3 text-right font-medium text-gray-500 dark:text-gray-400 sm:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/3">
                                <td class="px-5 py-3 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white uppercase">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                                            @if ($user->phone)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-5 py-3">
                                    @if ($user->role)
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($user->is_admin)
                                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Super Admin</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">Staff</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-right sm:px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                            Edit
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="px-5 py-4 sm:px-6 border-t border-gray-100 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
