<?php

namespace App\Livewire\Admin\Shared;

use App\Domains\Notification\Models\AppNotification;
use Livewire\Component;

/**
 * Bell-icon notification dropdown for the admin nav bar.
 *
 * Usage: <livewire:admin.shared.notification-bell />
 */
class NotificationBell extends Component
{
    public bool $open = false;

    protected $listeners = ['notificationReceived' => '$refresh'];

    public function toggle(): void
    {
        $this->open = ! $this->open;

        // Mark all as read when opening
        if ($this->open) {
            AppNotification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function markRead(int $id): void
    {
        AppNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = $notifications->whereNull('read_at')->count();

        return view('livewire.admin.shared.notification-bell', compact('notifications', 'unreadCount'));
    }
}
