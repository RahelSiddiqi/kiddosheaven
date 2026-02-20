<div x-data="{ selectedRole: '' }">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Permissions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage system permissions and assign to roles</p>
        </div>
        <div class="flex items-center gap-2">
            <button
                wire:click="generate"
                wire:confirm="Generate missing permissions from all areas? This will create new permissions for all system modules."
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition"
            >
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="generate" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span wire:loading.remove wire:target="generate">Generate Permissions</span>
                <span wire:loading wire:target="generate">Generating...</span>
            </button>
            <button wire:click="createModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Permission
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        {{-- Search --}}
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search permissions..." class="pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none w-full" />
        </div>

        {{-- Group Filter --}}
        <select wire:model.live="group" class="px-3 py-2 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none">
            <option value="">All Groups</option>
            @foreach($groups as $g)
                <option value="{{ $g }}">{{ ucfirst(str_replace('-', ' ', $g)) }}</option>
            @endforeach
        </select>

        {{-- Assign to Role --}}
        <div class="flex items-center gap-2">
            <select x-model="selectedRole" class="px-3 py-2 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none min-w-[160px]">
                <option value="">Select Role...</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <button
                @click="if(selectedRole) { $wire.openAssignModal(parseInt(selectedRole)); selectedRole = ''; }"
                :disabled="!selectedRole"
                class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Assign Permissions
            </button>
        </div>
    </div>

    {{-- Permissions Grouped --}}
    @forelse($permissions as $groupName => $groupPermissions)
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ ucfirst(str_replace('-', ' ', $groupName ?? 'General')) }}
                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $groupPermissions->count() }})</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($groupPermissions as $permission)
                    <div wire:key="perm-{{ $permission->id }}" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-sm transition">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $permission->name }}</p>
                                <p class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">{{ $permission->slug }}</p>
                                @if($permission->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $permission->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 ml-2 shrink-0">
                                <button wire:click="editModal({{ $permission->id }})" class="p-1.5 rounded-lg text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteRecord({{ $permission->id }})" wire:confirm="Delete permission '{{ addslashes($permission->name) }}'? This will remove it from all roles." class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <p class="font-medium text-gray-900 dark:text-white">No permissions found</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Click "Generate Permissions" to create default permissions or add one manually</p>
        </div>
    @endforelse

    {{-- Create / Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Edit Permission' : 'New Permission' }}</h3>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="e.g., View Products" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @if($name)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Slug: <span class="font-mono">{{ Str::slug($name) }}</span></p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Group</label>
                        <input wire:model="permGroup" type="text" list="existing-groups" placeholder="e.g., products" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                        <datalist id="existing-groups">
                            @foreach($groups as $g)
                                <option value="{{ $g }}">
                            @endforeach
                        </datalist>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty for "general" group</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea wire:model="description" rows="2" placeholder="Optional description..." class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update' : 'Create' }}</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Assign Modal --}}
    @if($showAssignModal)
        @php $assignRole = $roles->firstWhere('id', $assignRoleId); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeAssignModal"></div>
            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden max-h-[90vh] flex flex-col">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Assign Permissions to Role</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $assignRole?->name ?? 'Unknown' }}</p>
                    </div>
                    <button wire:click="closeAssignModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Permissions List --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="mb-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Selected:</strong> {{ count($assignedPermIds) }} of {{ $allPermissions->flatten()->count() }} permissions
                        </p>
                    </div>

                    @foreach($allPermissions as $groupName => $groupPerms)
                        <div x-data="{ expanded: true }" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            {{-- Group Header --}}
                            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700/50 cursor-pointer" @click="expanded = !expanded">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ ucfirst(str_replace('-', ' ', $groupName)) }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $groupPerms->count() }})</span>
                                </div>
                                <button
                                    type="button"
                                    class="text-xs text-[var(--color-primary)] hover:underline"
                                    @click.stop="
                                        const groupIds = {{ json_encode($groupPerms->pluck('id')->map(fn($id) => (string)$id)->toArray()) }};
                                        const currentSelected = $wire.assignedPermIds;
                                        const allSelected = groupIds.every(id => currentSelected.includes(id));
                                        if (allSelected) {
                                            $wire.assignedPermIds = currentSelected.filter(id => !groupIds.includes(id));
                                        } else {
                                            const newSelection = [...new Set([...currentSelected, ...groupIds])];
                                            $wire.assignedPermIds = newSelection;
                                        }
                                    "
                                >
                                    Toggle All
                                </button>
                            </div>
                            {{-- Group Permissions --}}
                            <div x-show="expanded" x-collapse class="px-3 py-2 grid grid-cols-2 gap-1">
                                @foreach($groupPerms as $perm)
                                    <label class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="assignedPermIds"
                                            value="{{ $perm->id }}"
                                            class="rounded accent-[var(--color-primary)]"
                                        />
                                        <span class="text-sm text-gray-900 dark:text-white truncate">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
                    <button type="button" wire:click="closeAssignModal" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Cancel</button>
                    <button wire:click="saveAssign" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveAssign">Save Assignments</span>
                        <span wire:loading wire:target="saveAssign">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
