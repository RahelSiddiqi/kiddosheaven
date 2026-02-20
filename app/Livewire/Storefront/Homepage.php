<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Collection;
use App\Domains\Product\Models\Product;
use App\Domains\Marketing\Models\FlashSale;

class Homepage extends Component
{
    public $featuredByCategory = [];
    public $featuredCollections = [];
    public $flashSale = null;
    public $newArrivals = [];
    public $bestSellers = [];
    public $allFeatured = [];

    public function mount()
    {
        $this->loadHomeData();
    }

    public function loadHomeData()
    {
        // Featured products by category (if show_on_home column exists)
        try {
            $homeCategories = Category::where('show_on_home', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($homeCategories as $category) {
                $products = $category->products()
                    ->where('is_active', true)
                    ->where('is_featured', true)
                    ->take(4)
                    ->get();

                if ($products->isNotEmpty()) {
                    $this->featuredByCategory[$category->name] = $products;
                }
            }
        } catch (\Exception $e) {
            // Column might not exist yet, skip
        }

        // Flash sales (if table exists)
        try {
            $this->featuredCollections = Collection::active()
                ->featured()
                ->withCount('products')
                ->orderBy('position')
                ->take(6)
                ->get();
        } catch (\Exception $e) {
            $this->featuredCollections = collect();
        }

        // Flash sales (if table exists)
        try {
            $this->flashSale = FlashSale::active()
                ->with(['products' => function($query) {
                    $query->where('is_active', true)->take(8);
                }])
                ->first();
        } catch (\Exception $e) {
            $this->flashSale = null;
        }

        // New arrivals
        $this->newArrivals = Product::where('is_active', true)
            ->where('created_at', '>=', now()->subDays(14))
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Best sellers
        try {
            $this->bestSellers = Product::where('is_active', true)
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            // If orderItems relationship doesn't exist, just get recent products
            $this->bestSellers = Product::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        }

        // All featured products (fallback)
        $this->allFeatured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();
    }

    public function addToCart($productSlug)
    {
        $product = Product::where('slug', $productSlug)->first();

        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        $cart = session('cart', ['items' => []]);
        $key = $product->id;

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity']++;
        } else {
            $cart['items'][$key] = [
                'product_id' => $product->id,
                'variant_id' => null,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image' => $product->primary_image ?? ($product->images[0] ?? null),
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated');
        session()->flash('cart-success', 'Product added to cart!');
    }

    public function render()
    {
        view()->share('page_title', config('app.name') . ' - Kids Toys & Products');
        view()->share('page_description', 'Discover amazing toys and products for kids at ' . config('app.name'));

        return view('livewire.storefront.homepage');
    }
}
