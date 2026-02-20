<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.cms.pages.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $pageId ? 'Edit Page' : 'New Page' }}</h1>
            <p class="text-sm text-gray-500">{{ $pageId ? 'Update CMS page content' : 'Create a new CMS page' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Content --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Page Content</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input wire:model.live.debounce.400ms="title" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 font-mono">/page/</span>
                        <input wire:model="slug" type="text" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                    </div>
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Content</label>
                    <div class="border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-[var(--color-primary)]/30 focus-within:border-[var(--color-primary)]">
                        <textarea
                            wire:model="content"
                            rows="16"
                            placeholder="Write your page content here... HTML is supported."
                            class="w-full px-4 py-3 text-sm font-mono focus:outline-none resize-y"
                        ></textarea>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">HTML markup is supported.</p>
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">SEO</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Title <span class="text-gray-400 font-normal">(defaults to page title)</span></label>
                    <input wire:model="metaTitle" type="text" :placeholder="title" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Description</label>
                    <textarea wire:model="metaDescription" rows="2" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Settings</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="isActive" class="rounded accent-[var(--color-primary)]" />
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="showInFooter" class="rounded accent-[var(--color-primary)]" />
                    <span class="text-sm text-gray-700">Show in Footer</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="showInHeader" class="rounded accent-[var(--color-primary)]" />
                    <span class="text-sm text-gray-700">Show in Header</span>
                </label>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Sort Order</label>
                    <input wire:model="sortOrder" type="number" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center pb-8">
            @if($pageId)
                <a href="{{ route('page.show', $slug) }}" target="_blank" class="text-sm text-[var(--color-primary)] hover:underline inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Preview page
                </a>
            @else
                <span></span>
            @endif
            <div class="flex gap-3">
                <a href="{{ route('admin.cms.pages.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                    <span wire:loading.remove>{{ $pageId ? 'Update Page' : 'Create Page' }}</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
