<?php

namespace App\Support\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case MOBILE_BANKING = 'mobile_banking';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case COD = 'cod';
    case STRIPE = 'stripe';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::MOBILE_BANKING => 'Mobile Banking',
            self::CREDIT_CARD => 'Credit Card',
            self::DEBIT_CARD => 'Debit Card',
            self::COD => 'Cash on Delivery',
            self::STRIPE => 'Stripe',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CASH => 'heroicon-o-banknotes',
            self::BANK_TRANSFER => 'heroicon-o-building-library',
            self::MOBILE_BANKING => 'heroicon-o-device-phone-mobile',
            self::CREDIT_CARD => 'heroicon-o-credit-card',
            self::DEBIT_CARD => 'heroicon-o-credit-card',
            self::COD => 'heroicon-o-truck',
            self::STRIPE => 'heroicon-o-globe-alt',
        };
    }
}
