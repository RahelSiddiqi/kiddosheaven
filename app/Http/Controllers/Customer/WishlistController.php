<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display customer's wishlist
     */
    public function index()
    {
        $wishlistItems = Auth::user()->wishlist()
            ->with('product:id,name,slug,price,primary_image,stock_quantity')
            ->latest()
            ->get();

        return view('customer.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Toggle a product in/out of wishlist (AJAX)
     */
    public function toggle(Request $request, Product $product)
    {
        $query = Auth::user()->wishlist()->where('product_id', $product->id);
        $exists = $query->exists();

        if ($exists) {
            $query->delete();
            $inWishlist = false;
            $message = 'Removed from wishlist';
        } else {
            Auth::user()->wishlist()->create(['product_id' => $product->id]);
            $inWishlist = true;
            $message = 'Added to wishlist';
        }

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
            'message' => $message,
            'wishlist_count' => Auth::user()->wishlist()->count(),
        ]);
    }

    /**
     * Add a product to wishlist
     */
    public function add(Request $request, Product $product)
    {
        // Check if already in wishlist
        $exists = Auth::user()->wishlist()
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist',
            ], 400);
        }

        Auth::user()->wishlist()->create([
            'product_id' => $product->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'wishlist_count' => Auth::user()->wishlist()->count(),
        ]);
    }

    /**
     * Remove a product from wishlist
     */
    public function remove(Product $product)
    {
        $deleted = Auth::user()->wishlist()
            ->where('product_id', $product->id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not in wishlist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist',
            'wishlist_count' => Auth::user()->wishlist()->count(),
        ]);
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart(Product $product)
    {
        // Remove from wishlist
        Auth::user()->wishlist()
            ->where('product_id', $product->id)
            ->delete();

        // Add to cart (assuming you have a cart system)
        // This will depend on your cart implementation
        session()->push('cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Moved to cart',
            'wishlist_count' => Auth::user()->wishlist()->count(),
        ]);
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        Auth::user()->wishlist()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared',
        ]);
    }
}
