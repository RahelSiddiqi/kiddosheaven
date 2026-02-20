<?php

namespace App\Livewire\Storefront;

use App\Models\Review;
use App\Models\OrderItem;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ProductReviews extends Component
{
    use WithPagination;

    public int $productId;

    // Review form fields
    public int $rating = 5;
    public string $reviewTitle = '';
    public string $reviewContent = '';
    public bool $showForm = false;
    public bool $submitted = false;

    #[Computed]
    public function reviews()
    {
        return Review::where('product_id', $this->productId)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
    }

    #[Computed]
    public function averageRating(): float
    {
        return round(Review::getAverageRating($this->productId), 1);
    }

    #[Computed]
    public function reviewCount(): int
    {
        return Review::getReviewCount($this->productId);
    }

    #[Computed]
    public function ratingBreakdown(): array
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Review::where('product_id', $this->productId)
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
            $breakdown[$i] = [
                'count' => $count,
                'percent' => $this->reviewCount > 0 ? round(($count / $this->reviewCount) * 100) : 0,
            ];
        }
        return $breakdown;
    }

    #[Computed]
    public function hasReviewed(): bool
    {
        if (!auth()->check()) return false;
        return Review::where('product_id', $this->productId)
            ->where('user_id', auth()->id())
            ->exists();
    }

    #[Computed]
    public function hasPurchased(): bool
    {
        if (!auth()->check()) return false;

        try {
            // Check if user has a completed order containing this product
            return OrderItem::where('product_id', $this->productId)
                ->whereHas('order', fn($q) => $q->where('user_id', auth()->id())->whereIn('status', ['delivered', 'completed']))
                ->exists();
        } catch (\Exception $e) {
            // If OrderItem table structure differs, return false
            return false;
        }
    }

    public function submitReview(): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Please log in to write a review.', type: 'error');
            return;
        }

        if ($this->hasReviewed) {
            $this->dispatch('notify', message: 'You have already reviewed this product.', type: 'error');
            return;
        }

        $this->validate([
            'rating' => 'required|integer|between:1,5',
            'reviewTitle' => 'required|string|min:3|max:100',
            'reviewContent' => 'required|string|min:10|max:1000',
        ]);

        Review::create([
            'product_id' => $this->productId,
            'user_id' => auth()->id(),
            'rating' => $this->rating,
            'title' => $this->reviewTitle,
            'content' => $this->reviewContent,
            'is_approved' => false, // pending moderation
            'is_verified_purchase' => $this->hasPurchased,
        ]);

        $this->reset(['rating', 'reviewTitle', 'reviewContent', 'showForm']);
        $this->rating = 5;
        $this->submitted = true;
        unset($this->reviews, $this->averageRating, $this->reviewCount, $this->ratingBreakdown, $this->hasReviewed);
    }

    public function deleteReview(int $reviewId): void
    {
        if (!auth()->check()) {
            return;
        }

        $review = Review::where('id', $reviewId)
            ->where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if (!$review) {
            $this->dispatch('notify', message: 'Review not found.', type: 'error');
            return;
        }

        $review->delete();

        unset($this->reviews, $this->averageRating, $this->reviewCount, $this->ratingBreakdown, $this->hasReviewed);
        $this->dispatch('notify', message: 'Your review has been deleted.', type: 'success');
    }

    public function render()
    {
        return view('livewire.storefront.product-reviews');
    }
}
