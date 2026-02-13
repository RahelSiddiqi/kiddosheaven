<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $homeCategories = Category::where('show_on_home', true)->get();
        $featuredByCategory = [];
        foreach ($homeCategories as $category) {
            $featuredByCategory[$category->name] = $category->products()->where('is_featured', true)->take(4)->get();
        }
        return view('shop.home', [
            'featuredByCategory' => $featuredByCategory,
            'homeCategories' => $homeCategories,
        ]);
    }

    public function catalog(Request $request)
    {
        $categoryId = $request->query('category_id');

        $query = Product::query()->where('is_active', true)->orderBy('name');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('shop.catalog', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $categoryId,
        ]);
    }

    public function showProduct(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
        return view('shop.product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
