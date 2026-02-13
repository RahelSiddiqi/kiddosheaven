<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address_line',
        'city',
        'postal_code',
        'notes',
        'subtotal',
        'tax_amount',
        'total_amount',
        'payment_method',
        'status',
        'status_notes',
        'cancellation_reason',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer's shipping address.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
