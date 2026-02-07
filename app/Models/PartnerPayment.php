<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'amount',
        'payment_date',
        'reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the partner for this payment.
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
            self::STATUS_COMPLETED => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
            self::STATUS_PENDING => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-500',
            default => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
        };
    }
}
