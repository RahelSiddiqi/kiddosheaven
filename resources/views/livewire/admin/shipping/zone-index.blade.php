<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Shipping Zones</h1>
        <button wire:click="openCreate" class="btn-primary">+ Add Zone</button>
    </div>

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ $editingId ? 'Edit Zone' : 'New Zone' }}</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Zone Name</label>
                <input wire:model="name" type="text" class="input w-full" placeholder="e.g. Bangladesh, South Asia" />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_rest_of_world" type="checkbox" class="checkbox">
                    <span class="text-sm">Rest of World (catch-all)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_active" type="checkbox" class="checkbox">
                    <span class="text-sm">Active</span>
                </label>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="save" class="btn-primary">Save</button>
            <button wire:click="$set('showForm', false)" class="btn-secondary">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search zones..." class="input w-72" />
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Rates</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($zones as $zone)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $zone->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $zone->rates->count() }} rates</td>
                    <td class="px-4 py-3">
                        {{ $zone->is_rest_of_world ? 'Rest of World' : 'Specific Countries' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $zone->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $zone->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="edit({{ $zone->id }})" class="text-blue-600 hover:underline mr-2">Edit</button>
                        <button wire:click="delete({{ $zone->id }})" wire:confirm="Delete this zone?" class="text-red-500 hover:underline">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No shipping zones found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $zones->links() }}</div>
    </div>
</div>
