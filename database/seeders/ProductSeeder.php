<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Teddy Bear',
                'slug' => 'teddy-bear',
                'category' => 'Stuffed Animals',
                'price' => 3000,
                'short_description' => 'Soft and cuddly classic teddy bear.',
                'description' => 'A timeless teddy bear made from ultra-soft fabric, perfect for hugs and bedtime stories.',
                'images' => ['products/1.jpeg'],
                'primary_image' => 'products/1.jpeg',
                'is_featured' => true,
            ],
            [
                'name' => 'Mega Plush Toy',
                'slug' => 'mega-plush-toy',
                'category' => 'Stuffed Animals',
                'price' => 3800,
                'short_description' => 'Oversized plush friend for extra big cuddles.',
                'description' => 'This mega plush is the ultimate snuggle buddy for kids, with bright colors and a friendly face.',
                'images' => ['products/2.jpeg'],
                'primary_image' => 'products/2.jpeg',
                'is_featured' => true,
            ],
            [
                'name' => 'Cute Dog',
                'slug' => 'cute-dog',
                'category' => 'Stuffed Animals',
                'price' => 2400,
                'short_description' => 'Floppy-eared puppy plush toy.',
                'description' => 'A sweet little pup made from hypoallergenic materials, safe for all ages.',
                'images' => ['products/3.jpeg'],
                'primary_image' => 'products/3.jpeg',
                'is_featured' => false,
            ],
            [
                'name' => 'Little Friend',
                'slug' => 'little-friend',
                'category' => 'Stuffed Animals',
                'price' => 2700,
                'short_description' => 'Small companion plush, perfect for travel.',
                'description' => 'This little friend is easy to carry and perfect for everyday adventures.',
                'images' => ['products/4.jpeg'],
                'primary_image' => 'products/4.jpeg',
                'is_featured' => false,
            ],
            [
                'name' => 'Happy Flower',
                'slug' => 'happy-flower',
                'category' => 'Wooden Toys',
                'price' => 3800,
                'short_description' => 'Colorful wooden stacking flower.',
                'description' => 'A cheerful wooden stacking toy that helps develop coordination and color recognition.',
                'images' => ['products/5.jpeg'],
                'primary_image' => 'products/5.jpeg',
                'is_featured' => true,
            ],
            [
                'name' => 'Lift Machine',
                'slug' => 'lift-machine',
                'category' => 'Wooden Toys',
                'price' => 2400,
                'short_description' => 'Wooden construction lift truck.',
                'description' => 'Sturdy wooden construction truck designed for imaginative building play.',
                'images' => ['products/6.jpeg'],
                'primary_image' => 'products/6.jpeg',
                'is_featured' => false,
            ],
            [
                'name' => 'Wooden Camera',
                'slug' => 'wooden-camera',
                'category' => 'Wooden Toys',
                'price' => 3200,
                'short_description' => 'Pretend-play wooden camera with click button.',
                'description' => 'Encourage imaginative play with this beautifully crafted wooden camera.',
                'images' => ['products/7.jpeg'],
                'primary_image' => 'products/7.jpeg',
                'is_featured' => false,
            ],
            [
                'name' => 'Little Rabbit',
                'slug' => 'little-rabbit',
                'category' => 'Wooden Toys',
                'price' => 1600,
                'short_description' => 'Pull-along wooden rabbit toy.',
                'description' => 'A sweet pull-along rabbit that helps toddlers with early walking and balance.',
                'images' => ['products/8.jpeg'],
                'primary_image' => 'products/8.jpeg',
                'is_featured' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
