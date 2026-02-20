<?php

namespace App\Domains\Shipping\Services;

use App\Domains\Shipping\Models\ShippingRate;
use App\Domains\Shipping\Models\ShippingZone;
use App\Domains\Shipping\Models\Shipment;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Collection;

class ShippingService
{
    /**
     * Get all applicable shipping rates for a cart going to a given country.
     *
     * @param  string  $countryCode  ISO 3166-1 alpha-2 (e.g. 'BD')
     * @param  float   $orderAmount  Subtotal of the order
     * @param  float   $weight       Total weight in kg
     * @return Collection<ShippingRate>
     */
    public function getRatesForCountry(
        string $countryCode,
        float $orderAmount = 0,
        float $weight = 0
    ): Collection {
        $zone = ShippingZone::forCountry($countryCode);

        if (!$zone) {
            return collect();
        }

        return $zone->activeRates()
            ->where(function ($q) use ($orderAmount) {
                $q->whereNull('min_order_amount')
                  ->orWhere('min_order_amount', '<=', $orderAmount);
            })
            ->where(function ($q) use ($orderAmount) {
                $q->whereNull('max_order_amount')
                  ->orWhere('max_order_amount', '>=', $orderAmount);
            })
            ->where(function ($q) use ($weight) {
                $q->whereNull('min_weight')
                  ->orWhere('min_weight', '<=', $weight);
            })
            ->where(function ($q) use ($weight) {
                $q->whereNull('max_weight')
                  ->orWhere('max_weight', '>=', $weight);
            })
            ->get()
            ->map(function (ShippingRate $rate) use ($orderAmount, $weight) {
                $rate->calculated_cost = $rate->calculateCost($orderAmount, $weight);
                return $rate;
            });
    }

    /**
     * Get the cheapest rate for a country (used as default).
     */
    public function getCheapestRate(string $countryCode, float $orderAmount = 0, float $weight = 0): ?ShippingRate
    {
        return $this->getRatesForCountry($countryCode, $orderAmount, $weight)
            ->sortBy('calculated_cost')
            ->first();
    }

    /**
     * Create a shipment for an order.
     */
    public function createShipment(Order $order, array $data): Shipment
    {
        $shipment = $order->shipments()->create([
            'shipping_rate_id'  => $data['shipping_rate_id'] ?? null,
            'tracking_number'   => $data['tracking_number'] ?? null,
            'carrier'           => $data['carrier'] ?? null,
            'carrier_service'   => $data['carrier_service'] ?? null,
            'status'            => 'processing',
            'weight'            => $data['weight'] ?? null,
            'shipping_cost'     => $data['shipping_cost'] ?? 0,
            'notes'             => $data['notes'] ?? null,
            'estimated_delivery'=> $data['estimated_delivery'] ?? null,
        ]);

        // Add all unfulfilled order items to the shipment by default
        if (empty($data['items'])) {
            foreach ($order->items as $item) {
                $shipment->items()->create([
                    'order_item_id' => $item->id,
                    'quantity'      => $item->quantity,
                ]);
            }
        }

        // Update order fulfillment status
        $order->update(['fulfillment_status' => 'processing']);

        return $shipment;
    }

    /**
     * Mark a shipment as shipped and update the parent order.
     */
    public function markShipped(Shipment $shipment, string $trackingNumber = null, string $carrier = null): void
    {
        $shipment->markShipped($trackingNumber, $carrier);
    }

    /**
     * Mark a shipment as delivered.
     */
    public function markDelivered(Shipment $shipment): void
    {
        $shipment->markDelivered();
    }

    /**
     * Recalculate the order's fulfillment_status based on its shipments.
     * Supports partial fulfillment.
     */
    public function syncOrderFulfillmentStatus(Order $order): void
    {
        $totalItems     = $order->items->sum('quantity');
        $shippedItems   = $order->shipments()
            ->whereIn('status', ['in_transit', 'out_for_delivery', 'delivered'])
            ->with('items')
            ->get()
            ->flatMap(fn($s) => $s->items)
            ->sum('quantity');

        $deliveredItems = $order->shipments()
            ->where('status', 'delivered')
            ->with('items')
            ->get()
            ->flatMap(fn($s) => $s->items)
            ->sum('quantity');

        $status = match (true) {
            $deliveredItems >= $totalItems => 'delivered',
            $shippedItems   >= $totalItems => 'shipped',
            $shippedItems    >  0          => 'partial',
            default                        => 'unfulfilled',
        };

        $order->update(['fulfillment_status' => $status]);
    }
}
