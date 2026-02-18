<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        // Check if user already reviewed this product
        $existing = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return redirect()->route('products.show', $product->slug)
                ->with('review_success', 'You have already reviewed this product.')
                ->withFragment('reviews');
        }

        // Check if user purchased this product
        $hasPurchased = \App\Models\OrderItem::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id())
              ->whereIn('status', ['delivered', 'completed']);
        })->where('product_id', $product->id)->exists();

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_approved' => false,
            'is_verified_purchase' => $hasPurchased,
        ]);

        return redirect()->route('products.show', $product->slug)
            ->with('review_success', 'Thank you for your review! It will appear after approval.')
            ->withFragment('reviews');
    }
}
