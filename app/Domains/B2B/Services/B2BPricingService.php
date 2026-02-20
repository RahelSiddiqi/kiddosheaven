<?php

namespace App\Domains\B2B\Services;

use App\Domains\B2B\Models\CustomerGroup;
use App\Domains\B2B\Models\PriceList;
use App\Domains\B2B\Models\PriceListItem;
use App\Models\User;

/**
 * B2B pricing service — resolves the correct price for a customer.
 *
 * Priority: price list item > group discount % > standard price
 */
class B2BPricingService
{
    /**
     * Resolve the effective price for a product/variant for a given customer.
     *
     * @param  int        $productId
     * @param  float      $basePrice     Standard retail price
     * @param  int|null   $variantId
     * @param  User|null  $user
     * @return float
     */
    public function resolvePrice(int $productId, float $basePrice, ?int $variantId = null, ?User $user = null): float
    {
        if (! $user || ! $user->customer_group_id) {
            return $basePrice;
        }

        $group = CustomerGroup::find($user->customer_group_id);
        if (! $group || ! $group->is_active) {
            return $basePrice;
        }

        // 1. Check active price lists for this group
        $list = PriceList::where('customer_group_id', $group->id)->active()->first();

        if ($list) {
            $price = $list->priceFor($productId, $variantId);
            if ($price !== null) {
                return (float) $price;
            }
        }

        // 2. Blanket discount
        if ($group->discount_percent > 0) {
            return round($basePrice * (1 - $group->discount_percent / 100), 2);
        }

        return $basePrice;
    }

    /**
     * Assign a customer to a group.
     */
    public function assignGroup(User $user, int $groupId): void
    {
        $user->update(['customer_group_id' => $groupId]);
    }

    /**
     * Get (or create) the default customer group.
     */
    public function defaultGroup(int $siteId): CustomerGroup
    {
        return CustomerGroup::firstOrCreate(
            ['site_id' => $siteId, 'is_default' => true],
            ['name' => 'Retail', 'slug' => 'retail', 'is_active' => true],
        );
    }

    /**
     * Upsert a price list item.
     */
    public function setPriceListItem(PriceList $list, int $productId, float $price, ?int $variantId = null, int $minQty = 1): PriceListItem
    {
        return PriceListItem::updateOrCreate(
            [
                'price_list_id'      => $list->id,
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
            ],
            ['price' => $price, 'min_qty' => $minQty]
        );
    }
}
