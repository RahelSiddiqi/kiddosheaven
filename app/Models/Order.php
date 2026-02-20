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

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
     * Get the order status history.
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
