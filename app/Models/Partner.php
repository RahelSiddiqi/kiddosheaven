<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'contact_info',
        'bank_details',
        'commission_rate',
        'status',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'contact_info' => 'array',
        'bank_details' => 'array',
    ];

    const TYPE_SUPPLIER = 'supplier';
    const TYPE_AFFILIATE = 'affiliate';
    const TYPE_FRANCHISE = 'franchise';
    const TYPE_EMPLOYEE = 'employee';
    const TYPE_SERVICE_PROVIDER = 'service_provider';
    const TYPE_RESELLER = 'reseller';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the purchase batches for this partner.
     */
    public function purchaseBatches()
    {
        return $this->hasMany(PurchaseBatch::class);
    }

    /**
     * Get the expenses for this partner.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the payments for this partner.
     */
    public function payments()
    {
        return $this->hasMany(PartnerPayment::class);
    }

    /**
     * Get the calculations for this partner.
     */
    public function calculations()
    {
        return $this->hasMany(PartnerCalculation::class);
    }

    /**
     * Get contact info as array.
     */
    public function getContactInfoArrayAttribute()
    {
        if (is_string($this->contact_info)) {
            return json_decode($this->contact_info, true) ?? [];
        }
        return $this->contact_info ?? [];
    }

    /**
     * Get badge class for status.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            'suspended' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
