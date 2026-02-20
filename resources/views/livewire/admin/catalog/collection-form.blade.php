<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.collections.index') }}" class="text-gray-500 hover:text-gray-700">← Collections</a>
        <h1 class="text-2xl font-bold">{{ $collectionId ? 'Edit Collection' : 'New Collection' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="font-semibold text-gray-700">Details</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Title *</label>
                <input wire:model.live="name" type="text" class="input w-full" />
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input wire:model="slug" type="text" class="input w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea wire:model="description" rows="3" class="input w-full"></textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="font-semibold text-gray-700">Collection Type</h2>
            <div class="flex gap-4">
                <label class="flex items-start gap-3 cursor-pointer border rounded-lg p-4 flex-1 {{ $type === 'manual' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                    <input wire:model.live="type" type="radio" value="manual" class="mt-1" />
                    <div>
                        <p class="font-medium">Manual</p>
                        <p class="text-sm text-gray-500">Add products one by one.</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer border rounded-lg p-4 flex-1 {{ $type === 'automatic' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                    <input wire:model.live="type" type="radio" value="automatic" class="mt-1" />
                    <div>
                        <p class="font-medium">Automatic</p>
                        <p class="text-sm text-gray-500">Products matching conditions are added automatically.</p>
                    </div>
                </label>
            </div>

            @if($type === 'automatic')
            <div class="space-y-3 mt-4">
                <div class="flex items-center gap-2 text-sm">
                    <span>Products must match</span>
                    <select wire:model="condition_match" class="input w-20">
                        <option value="all">all</option>
                        <option value="any">any</option>
                    </select>
                    <span>conditions:</span>
                </div>
                @foreach($conditions as $i => $condition)
                <div class="flex items-center gap-2">
                    <select wire:model="conditions.{{ $i }}.field" class="input w-36">
                        <option value="title">Title</option>
                        <option value="price">Price</option>
                        <option value="category">Category</option>
                        <option value="vendor">Brand/Vendor</option>
                    </select>
                    <select wire:model="conditions.{{ $i }}.operator" class="input w-36">
                        <option value="contains">contains</option>
                        <option value="equals">is equal to</option>
                        <option value="not_equals">is not equal to</option>
                        <option value="starts_with">starts with</option>
                        <option value="greater_than">is greater than</option>
                        <option value="less_than">is less than</option>
                    </select>
                    <input wire:model="conditions.{{ $i }}.value" type="text" class="input flex-1" placeholder="Value..." />
                    <button type="button" wire:click="removeCondition({{ $i }})" class="text-red-400 hover:text-red-600">✕</button>
                </div>
                @endforeach
                <button type="button" wire:click="addCondition" class="text-sm text-indigo-600 hover:underline">+ Add condition</button>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="font-semibold text-gray-700">Sort & Visibility</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Default Sort</label>
                    <select wire:model="sort_order" class="input w-full">
                        <option value="manual">Manual</option>
                        <option value="best_selling">Best Selling</option>
                        <option value="title_asc">A–Z</option>
                        <option value="title_desc">Z–A</option>
                        <option value="price_asc">Price: Low → High</option>
                        <option value="price_desc">Price: High → Low</option>
                        <option value="created_desc">Newest First</option>
                        <option value="created_asc">Oldest First</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_active" type="checkbox" class="checkbox" />
                    <span class="text-sm font-medium">Active (visible in store)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_featured" type="checkbox" class="checkbox" />
                    <span class="text-sm font-medium">Featured</span>
                </label>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="font-semibold text-gray-700">SEO</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Title</label>
                <input wire:model="meta_title" type="text" class="input w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Description</label>
                <textarea wire:model="meta_description" rows="2" class="input w-full"></textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Save Collection</button>
            <a href="{{ route('admin.collections.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
