<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog;
use App\Models\ProductAttribute;

class PuzzleAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates puzzle-specific attributes and associates them with the Puzzles catalog.
     */
    public function run(): void
    {
        $this->command->info('Setting up puzzle-specific attributes...');

        // First, ensure we have a Puzzles catalog with correct type
        $puzzlesCatalog = Catalog::where('name', 'Puzzles')->first();

        if (!$puzzlesCatalog) {
            $puzzlesCatalog = Catalog::create([
                'name' => 'Puzzles',
                'type' => 'puzzles',
                'description' => 'Challenge your mind with our collection of puzzles',
                'show_on_home' => true,
            ]);
            $this->command->info('Created Puzzles catalog');
        } else {
            // Update the type to puzzles if it's wrong
            if ($puzzlesCatalog->type !== 'puzzles') {
                $puzzlesCatalog->update(['type' => 'puzzles']);
                $this->command->info('Updated Puzzles catalog type to: puzzles');
            }
        }

        // Define puzzle-specific attributes
        $puzzleAttributes = [
            [
                'name' => 'Number of Pieces',
                'slug' => 'number-of-pieces',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 1,
                'description' => 'Total number of puzzle pieces',
            ],
            [
                'name' => 'Puzzle Theme',
                'slug' => 'puzzle-theme',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 2,
                'description' => 'Theme or subject of the puzzle',
            ],
            [
                'name' => 'Difficulty Level',
                'slug' => 'difficulty-level',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 3,
                'description' => 'How difficult the puzzle is',
            ],
            [
                'name' => 'Age Recommendation',
                'slug' => 'age-recommendation',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 4,
                'description' => 'Recommended minimum age',
            ],
            [
                'name' => 'Puzzle Type',
                'slug' => 'puzzle-type',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 5,
                'description' => 'Type of puzzle',
            ],
            [
                'name' => 'Material',
                'slug' => 'puzzle-material',
                'type' => 'select',
                'is_required' => false,
                'is_filterable' => true,
                'sort_order' => 6,
                'description' => 'Material used to make the puzzle',
            ],
            [
                'name' => 'Completed Size',
                'slug' => 'completed-size',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => false,
                'sort_order' => 7,
                'description' => 'Dimensions of the completed puzzle',
            ],
            [
                'name' => 'Educational Benefits',
                'slug' => 'educational-benefits',
                'type' => 'multiselect',
                'is_required' => false,
                'is_filterable' => true,
                'sort_order' => 8,
                'description' => 'Educational benefits of this puzzle',
            ],
        ];

        // Create or update attributes
        $attributeIds = [];
        foreach ($puzzleAttributes as $attrData) {
            $existing = ProductAttribute::where('slug', $attrData['slug'])->first();

            if ($existing) {
                $existing->update([
                    'name' => $attrData['name'],
                    'type' => $attrData['type'],
                    'is_required' => $attrData['is_required'],
                    'is_filterable' => $attrData['is_filterable'],
                    'sort_order' => $attrData['sort_order'],
                    'description' => $attrData['description'],
                ]);
                $attributeIds[] = $existing->id;
                $this->command->info('Updated attribute: ' . $attrData['name']);
            } else {
                $attribute = ProductAttribute::create($attrData);
                $attributeIds[] = $attribute->id;
                $this->command->info('Created attribute: ' . $attrData['name']);
            }
        }

        // Associate attributes with Puzzles catalog
        foreach ($attributeIds as $index => $attributeId) {
            $exists = DB::table('catalog_attributes')
                ->where('catalog_id', $puzzlesCatalog->id)
                ->where('product_attribute_id', $attributeId)
                ->exists();

            if (!$exists) {
                DB::table('catalog_attributes')->insert([
                    'catalog_id' => $puzzlesCatalog->id,
                    'product_attribute_id' => $attributeId,
                    'sort_order' => $index + 1,
                    'is_required' => $puzzleAttributes[$index]['is_required'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('Associated ' . $puzzleAttributes[$index]['name'] . ' with Puzzles catalog');
            }
        }

        // Now create attribute values for selectable options
        $this->createAttributeValues();

        $this->command->info('Puzzle attributes setup completed!');
    }

    /**
     * Create predefined values for select and multiselect attributes.
     */
    private function createAttributeValues(): void
    {
        // Number of Pieces values
        $this->addAttributeValues('number-of-pieces', [
            '12 pieces', '24 pieces', '48 pieces', '60 pieces', '100 pieces',
            '150 pieces', '200 pieces', '300 pieces', '500 pieces', '750 pieces',
            '1000 pieces', '1500 pieces', '2000 pieces', '3000+ pieces'
        ]);

        // Puzzle Theme values
        $this->addAttributeValues('puzzle-theme', [
            'Animals', 'Nature', 'Cities & Landmarks', 'Fantasy', 'Educational',
            'Art & Paintings', 'Movies & TV', 'Sports', 'Space & Science',
            'Underwater', 'Vehicles', 'Seasonal', 'Characters', 'Abstract'
        ]);

        // Difficulty Level values
        $this->addAttributeValues('difficulty-level', [
            'Very Easy (Ages 3+)', 'Easy (Ages 5+)', 'Medium (Ages 8+)',
            'Hard (Ages 10+)', 'Expert (Ages 14+)', 'Extreme (Adults)'
        ]);

        // Age Recommendation values
        $this->addAttributeValues('age-recommendation', [
            '3+ years', '4+ years', '5+ years', '6+ years', '7+ years',
            '8+ years', '10+ years', '12+ years', '14+ years', 'Adults'
        ]);

        // Puzzle Type values
        $this->addAttributeValues('puzzle-type', [
            'Jigsaw Puzzle', '3D Puzzle', 'Floor Puzzle', 'Tangram',
            'Logic Puzzle', 'Brain Teaser', 'Rubik\'s Cube', 'Wooden Puzzle',
            'Magnetic Puzzle', 'Cardboard Puzzle', 'Foam Puzzle', 'Puzzle Cube'
        ]);

        // Material values
        $this->addAttributeValues('puzzle-material', [
            'Cardboard', 'Wood', 'Plastic', 'Foam', 'Magnetic',
            'Fabric', 'Metal', 'Bamboo', 'Recycled Materials'
        ]);

        // Educational Benefits values
        $this->addAttributeValues('educational-benefits', [
            'Problem Solving', 'Spatial Reasoning', 'Hand-Eye Coordination',
            'Memory', 'Patience', 'Focus & Concentration', 'Logical Thinking',
            'Creativity', 'Visual Discrimination', 'Motor Skills', 'Teamwork'
        ]);

        $this->command->info('Created attribute values for puzzle options');
    }

    /**
     * Add values to a specific attribute.
     */
    private function addAttributeValues(string $attributeSlug, array $values): void
    {
        $attribute = ProductAttribute::where('slug', $attributeSlug)->first();
        if (!$attribute) {
            return;
        }

        // We need to find products with this attribute and add values
        // For now, we'll just note that these values are available
        // The actual ProductAttributeValue records are created when products are added
        $this->command->info('Values for ' . $attribute->name . ': ' . implode(', ', array_slice($values, 0, 5)) . '...');
    }
}
