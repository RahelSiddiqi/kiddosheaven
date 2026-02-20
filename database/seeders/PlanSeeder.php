<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'           => 'Starter',
                'slug'           => 'starter',
                'billing_period' => 'monthly',
                'price_cents'    => 2900,
                'features'       => json_encode([
                    'max_products'    => 100,
                    'max_staff'       => 2,
                    'custom_domain'   => false,
                    'api_access'      => false,
                    'analytics'       => 'basic',
                    'webhooks'        => false,
                    'app_marketplace' => false,
                ]),
                'is_active'   => true,
                'sort_order'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Growth',
                'slug'           => 'growth',
                'billing_period' => 'monthly',
                'price_cents'    => 7900,
                'features'       => json_encode([
                    'max_products'    => 1000,
                    'max_staff'       => 10,
                    'custom_domain'   => true,
                    'api_access'      => true,
                    'analytics'       => 'advanced',
                    'webhooks'        => true,
                    'app_marketplace' => true,
                ]),
                'is_active'   => true,
                'sort_order'  => 2,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Enterprise',
                'slug'           => 'enterprise',
                'billing_period' => 'monthly',
                'price_cents'    => 29900,
                'features'       => json_encode([
                    'max_products'    => -1,
                    'max_staff'       => -1,
                    'custom_domain'   => true,
                    'api_access'      => true,
                    'analytics'       => 'full',
                    'webhooks'        => true,
                    'app_marketplace' => true,
                    'dedicated_support' => true,
                    'b2b_wholesale'   => true,
                    'white_label'     => true,
                ]),
                'is_active'   => true,
                'sort_order'  => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(['slug' => $plan['slug']], $plan);
        }
    }
}
