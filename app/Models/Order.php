<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'address_line',
        'city',
        'postal_code',
        'notes',
        'total_amount',
        'payment_method',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
