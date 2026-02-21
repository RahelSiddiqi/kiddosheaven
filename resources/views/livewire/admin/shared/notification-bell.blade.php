<div class="relative" x-data>
    <!-- Bell button -->
    <button wire:click="toggle"
        class="relative flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    @if ($open)
        <div class="absolute right-0 top-12 z-50 w-80 rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900"
             wire:click.stop>
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h4>
                @if ($unreadCount > 0)
                    <span class="text-xs text-blue-600">{{ $unreadCount }} new</span>
                @endif
            </div>
            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800">
                @forelse ($notifications as $notif)
                    <div class="flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors {{ $notif->read_at ? 'opacity-60' : '' }}">
                        <!-- Icon -->
                        <div class="mt-0.5 flex-shrink-0 h-7 w-7 rounded-full flex items-center justify-center
                            {{ match(true) {
                                str_contains($notif->type, 'order') => 'bg-blue-100 dark:bg-blue-900/30',
                                str_contains($notif->type, 'stock') => 'bg-red-100 dark:bg-red-900/30',
                                str_contains($notif->type, 'review') => 'bg-yellow-100 dark:bg-yellow-900/30',
                                default => 'bg-gray-100 dark:bg-gray-700'
                            } }}">
                            <svg class="{{ match(true) {
                                str_contains($notif->type, 'order') => 'text-blue-500',
                                str_contains($notif->type, 'stock') => 'text-red-500',
                                str_contains($notif->type, 'review') => 'text-yellow-500',
                                default => 'text-gray-400'
                            } }}" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $notif->title }}</p>
                            @if ($notif->body)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $notif->body }}</p>
                            @endif
                            <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if (! $notif->read_at)
                            <div class="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full bg-blue-500"></div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">No notifications yet.</div>
                @endforelse
            </div>
            @if ($notifications->isNotEmpty())
                <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-800">
                    <a href="#" class="block text-center text-xs text-blue-600 hover:underline">View all</a>
                </div>
            @endif
        </div>
    @endif
</div>
