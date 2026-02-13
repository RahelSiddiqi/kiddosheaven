{{-- Relation Drawer / Slide-over Panel Component (Alpine.js powered)
    Usage:
    <x-admin.ui.relation-drawer id="batchDrawer" title="Batch Details">
        ... content ...
    </x-admin.ui.relation-drawer>

    Trigger: @click="$dispatch('open-drawer', { id: 'batchDrawer', data: {...} })"
--}}
@props(['id', 'title' => 'Details', 'width' => 'max-w-lg'])

<div x-data="{ open: false, data: {} }"
	x-on:open-drawer.window="if ($event.detail.id === '{{ $id }}') { data = $event.detail.data || {}; open = true; }"
	x-on:close-drawer.window="if ($event.detail?.id === '{{ $id }}' || !$event.detail?.id) { open = false; }"
	x-on:keydown.escape.window="open = false" x-cloak>

	{{-- Backdrop --}}
	<div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
		class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[9998]" @click="open = false"></div>

	{{-- Panel --}}
	<div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
		x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
		x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
		x-transition:leave-end="translate-x-full"
		class="fixed right-0 top-0 h-full {{ $width }} w-full bg-white dark:bg-gray-900 shadow-2xl z-[9999] flex flex-col border-l border-gray-200 dark:border-gray-800">

		{{-- Header --}}
		<div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
			<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h3>
			<button @click="open = false"
				class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-300 transition-colors">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		{{-- Content (scrollable) --}}
		<div class="flex-1 overflow-y-auto p-6">
			{{ $slot }}
		</div>
	</div>
</div>
