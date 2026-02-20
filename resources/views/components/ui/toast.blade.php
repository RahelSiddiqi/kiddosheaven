<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now();
            this.toasts.push({ id, ...toast });
            setTimeout(() => this.remove(id), toast.duration ?? 4000);
        },
        remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed bottom-4 right-4 z-[var(--z-toast)] flex flex-col gap-2 max-w-sm w-full pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{
                'bg-[var(--color-foreground)] text-white': toast.type === 'default',
                'bg-emerald-600 text-white': toast.type === 'success',
                'bg-red-600 text-white': toast.type === 'error',
                'bg-amber-500 text-white': toast.type === 'warning',
            }"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-[var(--shadow-lg)] border border-white/10"
        >
            <svg x-show="toast.type === 'success'" class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg x-show="toast.type === 'error'" class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p x-text="toast.title" class="font-medium text-sm" x-show="toast.title"></p>
                <p x-text="toast.message" class="text-sm opacity-90"></p>
            </div>
            <button type="button" x-on:click="remove(toast.id)" class="shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
