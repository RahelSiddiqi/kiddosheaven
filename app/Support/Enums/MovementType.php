<?php

namespace App\Support\Enums;

enum MovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';
    case TRANSFER = 'transfer';
    case DAMAGE = 'damage';
    case EXPIRE = 'expire';

    public function label(): string
    {
        return match($this) {
            self::PURCHASE => 'Purchase',
            self::SALE => 'Sale',
            self::ADJUSTMENT => 'Adjustment',
            self::RETURN => 'Return',
            self::TRANSFER => 'Transfer',
            self::DAMAGE => 'Damage',
            self::EXPIRE => 'Expire',
        };
    }

    public function isInflow(): bool
    {
        return in_array($this, [self::PURCHASE, self::RETURN]);
    }

    public function isOutflow(): bool
    {
        return in_array($this, [self::SALE, self::DAMAGE, self::EXPIRE]);
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PURCHASE => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::SALE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::ADJUSTMENT => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::RETURN => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::TRANSFER => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            self::DAMAGE => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::EXPIRE => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
        };
    }
}
