<?php

namespace App\Events;

use App\Domains\Shipping\Models\Shipment;
use Illuminate\Queue\SerializesModels;

class ShipmentShipped
{
    use SerializesModels;

    public function __construct(public readonly Shipment $shipment)
    {
    }
}
