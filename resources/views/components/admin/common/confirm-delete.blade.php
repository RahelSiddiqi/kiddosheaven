<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="delete-modal-title" role="dialog"
	aria-modal="true">
	<div class="flex min-h-screen items-center justify-center p-4">
		<div class="fixed inset-0 bg-black/50 transition-opacity" onclick="{{ $onCancel }}()"></div>
		<div class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-2xl">
			<div class="p-6 text-center">
				<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
					<svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
					</svg>
				</div>
				<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h3>
				<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
					{!! $message !!}
					<span id="delete-name" class="font-medium text-red-600"></span>
				</p>
				<div class="flex items-center justify-center gap-3">
					<button type="button" onclick="{{ $onCancel }}()"
						class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
						Cancel
					</button>
					<button type="button" onclick="{{ $onConfirm }}()"
						class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600">
						Delete
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
