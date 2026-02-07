<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCalculation extends Model
{
    use HasFactory;

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

    /**
     * Get the partner for this calculation.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PAID => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
            self::STATUS_APPROVED => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
            default => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-500',
        };
    }
}
