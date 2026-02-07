<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product', 'user')->latest();

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('product', function($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get stats
        $stats = [
            'total' => Review::count(),
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'avg_rating' => Review::approved()->avg('rating') ?? 0,
        ];

        $reviews = $query->paginate(20)->appends($request->all());

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(Review $review)
    {
        $review->load('product', 'user', 'order');
        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review rejected successfully.');
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
            'review_ids' => ['required', 'array'],
            'review_ids.*' => ['exists:reviews,id'],
        ]);

        $count = Review::whereIn('id', $validated['review_ids'])->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')
            ->with('success', "{$count} reviews approved successfully.");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => ['required', 'array'],
            'review_ids.*' => ['exists:reviews,id'],
        ]);

        $count = Review::whereIn('id', $validated['review_ids'])->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', "{$count} reviews deleted successfully.");
    }
}
