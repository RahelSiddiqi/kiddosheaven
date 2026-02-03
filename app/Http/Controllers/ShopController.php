<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $featuredStuffed = Product::where('category', 'Stuffed Animals')
            ->where('is_featured', true)
            ->take(4)
            ->get();

        $featuredWooden = Product::where('category', 'Wooden Toys')
            ->where('is_featured', true)
            ->take(4)
            ->get();

        return view('shop.home', [
            'featuredStuffed' => $featuredStuffed,
            'featuredWooden' => $featuredWooden,
        ]);
    }

    public function catalog(Request $request)
    {
        $category = $request->query('category');

        $query = Product::query()->orderBy('name');

        if ($category) {
            $query->where('category', $category);
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Product::select('category')->distinct()->pluck('category');

        return view('shop.catalog', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    public function showProduct(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $related = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
