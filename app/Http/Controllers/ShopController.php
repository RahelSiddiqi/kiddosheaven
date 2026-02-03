<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $homeCatalogs = \App\Models\Catalog::where('show_on_home', true)->get();
        $featuredByCatalog = [];
        foreach ($homeCatalogs as $catalog) {
            $featuredByCatalog[$catalog->name] = $catalog->products()->where('is_featured', true)->take(4)->get();
        }
        return view('shop.home', [
            'featuredByCatalog' => $featuredByCatalog,
            'homeCatalogs' => $homeCatalogs,
        ]);
    }

    public function catalog(Request $request)
    {
        $catalogId = $request->query('catalog_id');

        $query = Product::query()->orderBy('name');

        if ($catalogId) {
            $query->where('catalog_id', $catalogId);
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = \App\Models\Catalog::all();

        return view('shop.catalog', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $catalogId,
        ]);
    }

    public function showProduct(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $related = Product::where('catalog_id', $product->catalog_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
        return view('shop.product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
