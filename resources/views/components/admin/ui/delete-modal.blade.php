@props([
    'modalId' => 'deleteModal',
    'title' => 'Delete Item',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'deleteRoute' => '#',
    'itemId' => null,
    'itemName' => null,
])

<div x-data="{
    open: false,
    deleteUrl: '{{ $deleteRoute }}',
    itemId: {{ $itemId ?? 'null' }},
    itemName: '{{ $itemName ?? '' }}',
    isDeleting: false,
    openModal(url = null, id = null, name = null) {
        this.deleteUrl = url || '{{ $deleteRoute }}';
        this.itemId = id !== null ? id : {{ $itemId ?? 'null' }};
        this.itemName = name || '{{ $itemName ?? '' }}';
        this.open = true;
    },
    async confirmDelete() {
        if (!this.deleteUrl || this.isDeleting) return;

        this.isDeleting = true;
        try {
            const response = await fetch(this.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || ''
                }
            });

            const data = await response.json();

            if (data.success) {
                this.open = false;
                // Show success toast
                this.$dispatch('toast', { type: 'success', message: data.message || 'Item deleted successfully!' });
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.open = false;
                this.$dispatch('toast', { type: 'error', message: data.message || 'Error deleting item' });
            }
        } catch (error) {
            this.open = false;
            this.$dispatch('toast', { type: 'error', message: 'Error deleting item' });
        }
        this.isDeleting = false;
    }
}" x-show="open" x-cloak @keydown.escape.window="open = false"
	class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">

	<!-- Backdrop -->
	<div @click="open = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
		x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
	</div>

	<!-- Modal Content -->
	<div @click.stop class="relative w-full max-w-md rounded-3xl bg-white dark:bg-gray-900 p-6"
		x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
		x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">

		<!-- Close Button -->
		<button @click="open = false"
			class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fillRule="evenodd" clipRule="evenodd"
					d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
					fill="currentColor" />
			</svg>
		</button>

		<!-- Modal Body -->
		<div class="text-center">
			<!-- Icon -->
			<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
				<svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
				</svg>
			</div>

			<!-- Title -->
			<h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>

			<!-- Message -->
			<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>

			@if ($itemName)
				<p class="mb-6 text-sm font-medium text-gray-900 dark:text-white">"{{ $itemName }}"</p>
			@endif

			<!-- Buttons -->
			<div class="flex items-center justify-center gap-3">
				<button @click="open = false"
					class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					Cancel
				</button>
				<button @click="confirmDelete()" :disabled="isDeleting"
					class="h-10.5 inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
					<svg x-show="isDeleting" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor"
							d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
						</path>
					</svg>
					{{ $slot ?? 'Delete' }}
				</button>
			</div>
		</div>
	</div>
</div>

<style>
	[x-cloak] {
		display: none !important;
	}
</style>
