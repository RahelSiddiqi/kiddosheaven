<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportProducts extends Command
{
    protected $signature = 'products:import';
    protected $description = 'Import products from storage/app/public/products images';

    public function handle()
    {
        $productsPath = storage_path('app/public/products');
        
        if (!File::exists($productsPath)) {
            $this->error('Products directory not found!');
            return 1;
        }

        $images = File::glob($productsPath . '/*.{jpeg,jpg,png}', GLOB_BRACE);
        
        if (empty($images)) {
            $this->error('No product images found!');
            return 1;
        }

        $this->info("Found " . count($images) . " product images");

        $categories = ['Stuffed Animals', 'Wooden Toys', 'Educational Toys', 'Action Figures'];
        $names = [
            'Teddy Bear', 'Mega Plush Toy', 'Cute Dog', 'Little Friend', 'Happy Flower',
            'Lift Machine', 'Wooden Camera', 'Little Rabbit', 'Fluffy Bunny', 'Cuddly Panda',
            'Rainbow Unicorn', 'Soft Elephant', 'Playful Puppy', 'Sweet Kitty', 'Magic Dragon',
            'Wooden Train', 'Building Blocks', 'Puzzle Set', 'Musical Toy', 'Stacking Rings',
            'Colorful Ball', 'Toy Car', 'Doll House', 'Art Set', 'Science Kit',
            'Robot Friend', 'Space Explorer', 'Princess Doll', 'Superhero Figure', 'Fairy Tale Set',
            'Nature Explorer', 'Ocean Friend', 'Forest Animal', 'Jungle Buddy', 'Desert Companion',
            'Arctic Explorer', 'Farm Animal', 'Zoo Friend', 'Wildlife Set', 'Adventure Pack'
        ];

        $imported = 0;
        $skipped = 0;

        foreach ($images as $imagePath) {
            $filename = basename($imagePath);
            $imageId = (int) pathinfo($filename, PATHINFO_FILENAME);
            
            // Skip if product with this image already exists
            $existing = Product::where('image_path', '/storage/products/' . $filename)->first();
            if ($existing) {
                $skipped++;
                continue;
            }

            $nameIndex = ($imageId - 1) % count($names);
            $categoryIndex = ($imageId - 1) % count($categories);
            
            $name = $names[$nameIndex] . ' #' . $imageId;
            $slug = Str::slug($name);
            
            // Ensure unique slug
            $baseSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            Product::create([
                'name' => $name,
                'slug' => $slug,
                'category' => $categories[$categoryIndex],
                'price' => rand(1500, 5000), // Random price between $15 and $50
                'short_description' => 'A wonderful ' . strtolower($categories[$categoryIndex]) . ' perfect for playtime.',
                'description' => 'This delightful toy is designed to spark imagination and provide hours of fun. Made with care and attention to detail, it\'s perfect for children of all ages.',
                'image_path' => '/storage/products/' . $filename,
                'is_featured' => $imageId <= 8, // First 8 are featured
            ]);

            $imported++;
        }

        $this->info("Imported: {$imported} products");
        if ($skipped > 0) {
            $this->warn("Skipped: {$skipped} products (already exist)");
        }

        return 0;
    }
}
