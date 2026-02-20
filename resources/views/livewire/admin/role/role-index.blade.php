<div>
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Roles</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage user roles and their permissions</p>
        </div>
        <button wire:click="createModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Role
        </button>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $roles->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Roles</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $roles->where('users_count', '>', 0)->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Roles with Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $permissions->flatten()->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Permissions</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Slug</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Users</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Permissions</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Default</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                        <tr wire:key="role-{{ $role->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                @if($role->description)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($role->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $role->slug }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $role->users_count > 0 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $role->permissions_count > 0 ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                    {{ $role->permissions_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($role->is_default)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Default
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="editModal({{ $role->id }})" class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Edit Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($role->users_count > 0)
                                        <button disabled class="p-1.5 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Has {{ $role->users_count }} users - cannot delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <button wire:click="deleteRecord({{ $role->id }})" wire:confirm="Delete role '{{ addslashes($role->name) }}'? This action cannot be undone." class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition" title="Delete Role">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="font-medium">No roles found</p>
                                <p class="text-sm mt-1">Create your first role to get started</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Modal (Large with 2 columns) --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ allGroups: {} }">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative z-10 w-full max-w-4xl bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden max-h-[90vh] flex flex-col">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Edit Role' : 'New Role' }}</h3>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="save" class="flex-1 overflow-hidden flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Left Column: Role Info --}}
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Role Information</h4>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input wire:model.live.debounce.400ms="name" type="text" placeholder="e.g., Store Manager" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    @if($name)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Slug: <span class="font-mono">{{ Str::slug($name) }}</span></p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                    <textarea wire:model="description" rows="3" placeholder="Brief description of this role's responsibilities..." class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none resize-none"></textarea>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <input type="checkbox" wire:model="isDefault" id="role-default" class="rounded accent-[var(--color-primary)]" />
                                    <div>
                                        <label for="role-default" class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer">Set as Default Role</label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">New users will be assigned this role automatically</p>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        <strong>Selected:</strong> {{ count($selectedPermissions) }} permission(s)
                                    </p>
                                </div>
                            </div>

                            {{-- Right Column: Permissions --}}
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Permissions</h4>

                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden max-h-[400px] overflow-y-auto">
                                    @forelse($permissions as $group => $groupPermissions)
                                        <div x-data="{ expanded: true }" class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                            {{-- Group Header --}}
                                            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700/50 cursor-pointer" @click="expanded = !expanded">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ ucfirst(str_replace('-', ' ', $group ?? 'General')) }}</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $groupPermissions->count() }})</span>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="text-xs text-[var(--color-primary)] hover:underline"
                                                    @click.stop="
                                                        const groupIds = {{ json_encode($groupPermissions->pluck('id')->map(fn($id) => (string)$id)->toArray()) }};
                                                        const currentSelected = $wire.selectedPermissions;
                                                        const allSelected = groupIds.every(id => currentSelected.includes(id));
                                                        if (allSelected) {
                                                            $wire.selectedPermissions = currentSelected.filter(id => !groupIds.includes(id));
                                                        } else {
                                                            const newSelection = [...new Set([...currentSelected, ...groupIds])];
                                                            $wire.selectedPermissions = newSelection;
                                                        }
                                                    "
                                                >
                                                    Toggle All
                                                </button>
                                            </div>
                                            {{-- Group Permissions --}}
                                            <div x-show="expanded" x-collapse class="px-3 py-2 space-y-1">
                                                @foreach($groupPermissions as $permission)
                                                    <label class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            wire:model="selectedPermissions"
                                                            value="{{ $permission->id }}"
                                                            class="rounded accent-[var(--color-primary)]"
                                                        />
                                                        <div class="flex-1 min-w-0">
                                                            <span class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono ml-1">({{ $permission->slug }})</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            <p class="text-sm">No permissions available</p>
                                            <p class="text-xs mt-1">Generate permissions from the Permissions page first</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Role' : 'Create Role' }}</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
