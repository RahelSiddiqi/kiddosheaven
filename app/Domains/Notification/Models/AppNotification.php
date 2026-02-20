<?php

namespace App\Domains\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Concerns\BelongsToSite;

/**
 * In-app notification bell notifications for admin/merchant users.
 * Also used for customer account notifications.
 */
class AppNotification extends Model
{
    use BelongsToSite;

    protected $table = 'app_notifications';

    protected $fillable = [
        'site_id',
        'user_id',
        'type',       // order_placed | order_shipped | low_stock | new_review | return_requested | payment_received | etc.
        'title',
        'body',
        'icon',       // icon slug or emoji
        'url',        // action URL to navigate to
        'data',       // JSON extra data
        'read_at',
        'is_admin',   // true = admin notification, false = customer
    ];

    protected $casts = [
        'data'     => 'array',
        'read_at'  => 'datetime',
        'is_admin' => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeForCustomer($query)
    {
        return $query->where('is_admin', false);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
