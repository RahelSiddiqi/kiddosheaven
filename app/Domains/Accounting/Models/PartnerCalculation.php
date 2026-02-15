<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerCalculation extends Model
{
    protected $fillable = [
        'partner_id',
        'total_sales',
        'commission_amount',
        'payment_amount',
        'period_start',
        'period_end',
        'status',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
            self::STATUS_APPROVED => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
            default => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-500',
        };
    }
}
