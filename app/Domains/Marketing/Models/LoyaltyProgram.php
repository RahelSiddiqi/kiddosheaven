<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends Model
{
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

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function calculatePoints(float $amount): int
    {
        return (int) floor($amount * $this->points_per_currency);
    }

    public function calculateDiscount(int $points): float
    {
        return $points / $this->points_per_currency * $this->discount_percentage / 100;
    }

    public static function getActiveProgram(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
