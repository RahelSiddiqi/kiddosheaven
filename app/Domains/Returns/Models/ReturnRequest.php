<?php

namespace App\Domains\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Concerns\BelongsToSite;

class ReturnRequest extends Model
{
    use BelongsToSite, SoftDeletes;

    protected $fillable = [
        'site_id',
        'order_id',
        'user_id',
        'return_number',
        'type',
        'status',
        'reason',
        'customer_notes',
        'admin_notes',
        'refund_amount',
        'refund_method',
        'approved_at',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at'   => 'datetime',
        'received_at'   => 'datetime',
        'processed_at'  => 'datetime',
    ];

    const TYPES = [
        'refund'        => 'Refund',
        'exchange'      => 'Exchange',
        'store_credit'  => 'Store Credit',
    ];

    const STATUSES = [
        'pending'   => 'Pending Review',
        'approved'  => 'Approved',
        'declined'  => 'Declined',
        'received'  => 'Item Received',
        'processed' => 'Processed',
        'closed'    => 'Closed',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Order\Models\Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->return_number)) {
                $model->return_number = 'RET-' . strtoupper(uniqid());
            }
        });
    }

    public function approve(float $refundAmount = null, string $refundMethod = 'original_payment'): void
    {
        $this->update([
            'status'        => 'approved',
            'refund_amount' => $refundAmount ?? $this->calculateRefundAmount(),
            'refund_method' => $refundMethod,
            'approved_at'   => now(),
        ]);
    }

    public function decline(string $reason = null): void
    {
        $this->update([
            'status'      => 'declined',
            'admin_notes' => $reason ?? $this->admin_notes,
        ]);
    }

    public function markReceived(): void
    {
        $this->update(['status' => 'received', 'received_at' => now()]);
    }

    public function process(): void
    {
        $this->update(['status' => 'processed', 'processed_at' => now()]);
    }

    public function calculateRefundAmount(): float
    {
        return $this->items->sum(fn($item) => (float) ($item->refund_amount ?? 0));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
