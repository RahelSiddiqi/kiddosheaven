<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->latest()->paginate(15);

        $stats = [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'avg_rating' => Review::avg('rating') ?? 0,
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $validated['reviews'])->update(['is_approved' => true]);

        return back()->with('success', 'Selected reviews approved.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $validated['reviews'])->delete();

        return back()->with('success', 'Selected reviews deleted.');
    }

    public function productReviews(Request $request, $productId)
    {
        $query = Review::where('product_id', $productId)
            ->with(['user'])
            ->where('is_approved', true);

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(15);

        return view('admin.reviews.product-reviews', compact('reviews', 'productId'));
    }
}
