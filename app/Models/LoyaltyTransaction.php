<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyTransaction extends Model
{
    use HasFactory;

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

    protected $enumTypes = ['earned', 'redeemed', 'expired', 'bonus', 'adjustment'];

    /**
     * Get the user that owns the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loyalty program
     */
    public function program()
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }

    /**
     * Get the associated order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope for redeemed points
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Check if transaction adds points
     */
    public function isAddition(): bool
    {
        return in_array($this->type, ['earned', 'bonus']);
    }

    /**
     * Get user's current points balance
     */
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
