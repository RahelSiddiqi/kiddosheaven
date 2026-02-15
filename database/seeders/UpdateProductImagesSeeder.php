<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Product\Models\Product;
use Illuminate\Support\Facades\Storage;

class UpdateProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all product image files from storage
        $imageFiles = collect(Storage::disk('public')->allFiles('products'))
            ->filter(fn($path) => preg_match('/\.(jpg|jpeg|png|gif)$/i', $path))
            ->map(fn($path) => 'storage/' . $path)
            ->values()
            ->toArray();

        if (empty($imageFiles)) {
            $this->command->warn('No images found in storage/app/public/products/');
            return;
        }

        shuffle($imageFiles);

        // Better product names by category
        $productNames = [
            // Toys (cat 1)
            'Toys' => [
                'Rainbow Stacking Rings', 'Musical Safari Animals', 'Wooden Train Set',
                'Interactive Robot Buddy', 'Plush Elephant Teddy',
            ],
            // Puzzles (cat 2)
            'Puzzles' => [
                'Dinosaur Jigsaw Puzzle 100pc', 'World Map Floor Puzzle', 'ABC Learning Puzzle',
                'Animal Kingdom Puzzle', 'Solar System 3D Puzzle', 'Ocean Life Puzzle 200pc',
                'Farm Friends Puzzle', 'Space Explorer Puzzle',
            ],
            // Building Blocks (cat 3)
            'Building Blocks' => [
                'Classic Building Blocks 500pc', 'Castle Building Kit',
            ],
            // Clothing (cat 4)
            'Clothing' => [
                'Organic Cotton Baby Romper',
            ],
            // T-Shirts (cat 5)
            'T-Shirts' => [
                'Dinosaur Print T-Shirt', 'Rainbow Stars T-Shirt', 'Safari Adventure Tee',
                'Space Explorer T-Shirt', 'Cute Animals T-Shirt', 'Super Hero Graphic Tee',
            ],
            // Dresses (cat 6)
            'Dresses' => [
                'Floral Summer Dress',
            ],
        ];

        $products = Product::with('category')->get();
        $imageIndex = 0;

        foreach ($products as $product) {
            $catName = $product->category->name ?? 'Toys';
            $names = $productNames[$catName] ?? ['Fun Toy ' . $product->id];

            // Pick a name from available names for this category
            $nameIndex = 0;
            foreach ($productNames[$catName] ?? [] as $idx => $name) {
                // Find which index this product should use
                $sameCatProducts = $products->filter(fn($p) => ($p->category->name ?? '') === $catName)->values();
                $posInCat = $sameCatProducts->search(fn($p) => $p->id === $product->id);
                if ($posInCat !== false && isset($names[$posInCat])) {
                    $nameIndex = $posInCat;
                }
            }

            $newName = $names[$nameIndex] ?? $product->name;

            // Assign 3-5 random images per product
            $numImages = rand(3, 5);
            $productImages = [];
            for ($i = 0; $i < $numImages; $i++) {
                $productImages[] = $imageFiles[($imageIndex + $i) % count($imageFiles)];
            }

            $product->update([
                'name' => $newName,
                'slug' => \Str::slug($newName) . '-' . $product->id,
                'primary_image' => $productImages[0],
                'images' => $productImages, // Model casts to array, no json_encode needed
                'short_description' => $this->getDescription($catName, $newName),
                'description' => $this->getFullDescription($catName, $newName),
                'stock_quantity' => rand(5, 100),
            ]);

            $imageIndex += $numImages;
        }

        $this->command->info("Updated {$products->count()} products with real images and proper names.");
    }

    private function getDescription(string $category, string $name): string
    {
        $descriptions = [
            'Toys' => 'Fun and educational toy perfect for developing creativity and motor skills.',
            'Puzzles' => 'Engaging puzzle that builds problem-solving skills and patience.',
            'Building Blocks' => 'Creative building blocks for endless construction possibilities.',
            'Clothing' => 'Comfortable, high-quality clothing for your little one.',
            'T-Shirts' => 'Soft cotton t-shirt with vibrant, kid-friendly designs.',
            'Dresses' => 'Beautiful dress made from breathable, child-safe fabrics.',
        ];

        return $descriptions[$category] ?? "Quality product from Kiddo's Heaven.";
    }

    private function getFullDescription(string $category, string $name): string
    {
        return "Introducing {$name} - a premium quality product from Kiddo's Heaven. "
            . "Made with child-safe materials and designed to bring joy to kids of all ages. "
            . "Perfect for gifting or everyday fun. "
            . "All our products meet strict safety standards and are thoroughly tested.";
    }
}
