<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingTemplate;

class PricingTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Standard Retail Markup',
                'description' => 'Standard 50% markup for regular retail products',
                'strategy_type' => 'percentage_markup',
                'config' => ['percentage' => 50],
                'is_active' => true,
                'is_global' => true,
            ],
            [
                'name' => 'Premium Products',
                'description' => 'Higher 75% markup for premium/luxury items',
                'strategy_type' => 'percentage_markup',
                'config' => ['percentage' => 75],
                'is_active' => true,
                'is_global' => false,
            ],
            [
                'name' => 'Budget Line',
                'description' => 'Lower 30% markup for competitive pricing',
                'strategy_type' => 'percentage_markup',
                'config' => ['percentage' => 30],
                'is_active' => true,
                'is_global' => false,
            ],
            [
                'name' => 'Volume-Based Pricing',
                'description' => 'Decreasing markup percentage as cost increases',
                'strategy_type' => 'tiered',
                'config' => [
                    'tiers' => [
                        ['min_cost' => 200, 'percentage' => 35],
                        ['min_cost' => 100, 'percentage' => 45],
                        ['min_cost' => 50, 'percentage' => 55],
                        ['min_cost' => 0, 'percentage' => 65],
                    ],
                ],
                'is_active' => true,
                'is_global' => false,
            ],
            [
                'name' => 'Size-Based Pricing',
                'description' => 'Different markups based on product size',
                'strategy_type' => 'attribute_based',
                'config' => [
                    'rules' => [
                        [
                            'name' => 'Extra Large Items',
                            'conditions' => [
                                ['attribute' => 'Size', 'value' => 'XL'],
                            ],
                            'percentage' => 65,
                        ],
                        [
                            'name' => 'Large Items',
                            'conditions' => [
                                ['attribute' => 'Size', 'value' => 'L'],
                            ],
                            'percentage' => 55,
                        ],
                        [
                            'name' => 'Small Items',
                            'conditions' => [
                                ['attribute' => 'Size', 'value' => 'S'],
                            ],
                            'percentage' => 45,
                        ],
                    ],
                    'default_percentage' => 50,
                ],
                'is_active' => true,
                'is_global' => false,
            ],
            [
                'name' => 'Fixed $10 Markup',
                'description' => 'Simple fixed amount markup for low-cost items',
                'strategy_type' => 'fixed_markup',
                'config' => ['fixed_amount' => 10],
                'is_active' => true,
                'is_global' => false,
            ],
        ];

        foreach ($templates as $template) {
            PricingTemplate::create($template);
        }

        $this->command->info('✅ Created ' . count($templates) . ' pricing templates');
    }
}
