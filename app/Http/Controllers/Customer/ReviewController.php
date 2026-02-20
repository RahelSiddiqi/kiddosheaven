<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return back()->with('review_error', 'You have already reviewed this product.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return back()->with('review_success', 'Thank you! Your review has been submitted and is pending approval.');
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, Review $review)
    {
        // Ensure user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'status' => 'pending', // Reset to pending after update
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your review has been updated and is pending approval.',
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        // Ensure user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}
