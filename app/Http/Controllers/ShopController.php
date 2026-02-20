<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $homeCategories = Category::where('show_on_home', true)->orderBy('sort_order')->get();

        // Featured products by category (include child categories)
        $featuredByCategory = [];
        foreach ($homeCategories as $category) {
            // Get category IDs including all descendants
            $categoryIds = collect([$category])->merge($category->descendants())->pluck('id');

            $products = Product::whereIn('category_id', $categoryIds)
                ->where('is_active', true)
                ->where('is_featured', true)
                ->with(['brand', 'category', 'variants'])
                ->take(4)
                ->get();
            if ($products->isNotEmpty()) {
                $featuredByCategory[$category->name] = $products;
            }
        }

        // Flash sales (active)
        $flashSales = \App\Models\FlashSale::active()
            ->with(['products' => function ($query) {
                $query->where('is_active', true)->take(8);
            }])
            ->first();

        // New arrivals (last 14 days)
        $newArrivals = Product::where('is_active', true)
            ->where('created_at', '>=', now()->subDays(14))
            ->with(['brand', 'category', 'variants'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Best sellers (most ordered)
        $bestSellers = Product::where('is_active', true)
            ->withCount('orderItems')
            ->with(['brand', 'category', 'variants'])
            ->orderBy('order_items_count', 'desc')
            ->take(8)
            ->get();

        // All featured products (if no category-specific featured)
        $allFeatured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['brand', 'category', 'variants'])
            ->take(8)
            ->get();

        return view('shop.home', [
            'featuredByCategory' => $featuredByCategory,
            'homeCategories' => $homeCategories,
            'flashSales' => $flashSales,
            'newArrivals' => $newArrivals,
            'bestSellers' => $bestSellers,
            'allFeatured' => $allFeatured,
        ]);
    }

    public function catalog(Request $request)
    {
        $categoryId = $request->query('category_id');
        $brandId = $request->query('brand_id');
        $priceRange = $request->query('price');
        $ageGroup = $request->query('age');
        $featured = $request->query('featured');
        $newArrivals = $request->query('new');
        $onSale = $request->query('sale');
        $sort = $request->query('sort', 'newest');

        $query = Product::query()->where('is_active', true);

        // Category filter - if parent selected, include all children; if child selected, only that child
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                if ($category->parent_id === null) {
                    // Parent category: include products from parent and all children
                    $childIds = $category->children->pluck('id')->toArray();
                    $categoryIds = array_merge([$category->id], $childIds);
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    // Child category: only this category
                    $query->where('category_id', $categoryId);
                }
            }
        }

        // Brand filter
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        // Price range filter
        if ($priceRange) {
            switch ($priceRange) {
                case 'under-10':
                    $query->where('price', '<', 1000);
                    break;
                case '10-25':
                    $query->whereBetween('price', [1000, 2500]);
                    break;
                case '25-50':
                    $query->whereBetween('price', [2500, 5000]);
                    break;
                case 'over-50':
                    $query->where('price', '>', 5000);
                    break;
            }
        }

        // Featured filter
        if ($featured) {
            $query->where('is_featured', true);
        }

        // New arrivals filter
        if ($newArrivals) {
            $query->where('created_at', '>=', now()->subDays(14));
        }

        // On sale filter (has discount_price)
        if ($onSale) {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }

        // Sorting
        switch ($sort) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->with(['brand', 'category', 'variants'])->paginate(12)->withQueryString();

        // Get all parent categories with their children for hierarchy display
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $brands = \App\Models\Brand::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('shop.catalog', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'activeCategory' => $categoryId,
            'activeBrand' => $brandId,
            'activePrice' => $priceRange,
        ]);
    }

    public function showProduct(string $slug)
    {
        $product = Product::with(['category', 'brand', 'variants.variantAttributes'])->where('slug', $slug)->firstOrFail();
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['brand', 'category', 'variants'])
            ->take(4)
            ->get();

        // Get approved reviews
        $reviews = $product->approvedReviews()->with('user')->latest()->take(10)->get();

        // Get active variants if product is variable
        $variants = $product->product_type === 'variable'
            ? $product->activeVariants()->with('variantAttributes.attribute', 'variantAttributes.attributeValue')->get()
            : collect();

        // Check if user has this product in their wishlist
        $inWishlist = auth()->check()
            ? auth()->user()->wishlist()->where('product_id', $product->id)->exists()
            : false;

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
            'reviews' => $reviews,
            'variants' => $variants,
            'inWishlist' => $inWishlist,
        ]);
    }
}
