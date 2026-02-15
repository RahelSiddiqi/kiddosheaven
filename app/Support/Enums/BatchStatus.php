<?php

namespace App\Support\Enums;

enum BatchStatus: string
{
    case ACTIVE = 'active';
    case PARTIALLY_SOLD = 'partially_sold';
    case SOLD = 'sold';
    case EXPIRED = 'expired';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::PARTIALLY_SOLD => 'Partially Sold',
            self::SOLD => 'Sold Out',
            self::EXPIRED => 'Expired',
            self::DAMAGED => 'Damaged',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::PARTIALLY_SOLD => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::SOLD => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::EXPIRED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::DAMAGED => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
