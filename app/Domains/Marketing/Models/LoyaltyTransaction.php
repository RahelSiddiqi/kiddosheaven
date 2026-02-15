<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'loyalty_program_id',
        'type',
        'points',
        'order_id',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Order\Models\Order::class);
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    public function isAddition(): bool
    {
        return in_array($this->type, ['earned', 'bonus']);
    }

    public static function getUserBalance(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereIn('type', ['earned', 'bonus'])
            ->sum('points')
            - static::where('user_id', $userId)
            ->whereIn('type', ['redeemed', 'expired', 'adjustment'])
            ->sum('points');
    }
}
