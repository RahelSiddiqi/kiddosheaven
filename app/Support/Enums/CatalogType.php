<?php

namespace App\Support\Enums;

enum CatalogType: string
{
    case B2C = 'b2c';
    case B2B = 'b2b';
    case REGIONAL = 'regional';
    case WHOLESALE = 'wholesale';

    public function label(): string
    {
        return match($this) {
            self::B2C => 'B2C (Retail)',
            self::B2B => 'B2B (Business)',
            self::REGIONAL => 'Regional',
            self::WHOLESALE => 'Wholesale',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::B2C => 'Business to Consumer - Retail customers',
            self::B2B => 'Business to Business - Bulk orders and corporate clients',
            self::REGIONAL => 'Regional variations with different product sets',
            self::WHOLESALE => 'Wholesale pricing for resellers',
        };
    }
}
