<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'points_per_currency',
        'minimum_points',
        'discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get loyalty transactions
     */
    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    /**
     * Calculate points for amount
     */
    public function calculatePoints(float $amount): int
    {
        return (int) floor($amount * $this->points_per_currency);
    }

    /**
     * Calculate discount for points
     */
    public function calculateDiscount(int $points): float
    {
        return $points / $this->points_per_currency * $this->discount_percentage / 100;
    }

    /**
     * Get active program
     */
    public static function getActiveProgram(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
