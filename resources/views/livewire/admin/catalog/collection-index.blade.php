<div>
    @if(session('success'))
        <div class="bg-green-50 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Collections</h1>
        <a href="{{ route('admin.collections.create') }}" class="btn-primary">+ New Collection</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search collections..." class="input w-72" />
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Products</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($collections as $collection)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $collection->name }}</div>
                        <div class="text-xs text-gray-400">/collections/{{ $collection->slug }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $collection->type === 'automatic' ? 'badge-blue' : 'badge-gray' }}">
                            {{ ucfirst($collection->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $collection->products_count }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleActive({{ $collection->id }})" class="badge {{ $collection->is_active ? 'badge-green' : 'badge-gray' }} cursor-pointer">
                            {{ $collection->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.collections.edit', $collection->id) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                        @if($collection->isAutomatic())
                            <button wire:click="syncAutomatic({{ $collection->id }})" class="text-purple-600 hover:underline text-xs">Sync</button>
                        @endif
                        <button wire:click="delete({{ $collection->id }})" wire:confirm="Delete this collection?" class="text-red-500 hover:underline text-xs">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No collections yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $collections->links() }}</div>
    </div>
</div>
