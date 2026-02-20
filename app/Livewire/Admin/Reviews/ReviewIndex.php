<?php

namespace App\Livewire\Admin\Reviews;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Review;

#[Layout('admin.layouts.app')]
#[Title('Reviews - Admin')]
class ReviewIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $rating = '';

    public array $selectedIds = [];
    public bool $selectAll = false;

    #[Computed]
    public function reviews()
    {
        return Review::with(['product:id,name,slug', 'user:id,name,email'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('content', 'like', "%{$this->search}%")
                      ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                      ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($this->status === 'pending', fn($q) => $q->where('is_approved', false))
            ->when($this->rating, fn($q) => $q->where('rating', (int) $this->rating))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'avg_rating' => round(Review::where('is_approved', true)->avg('rating') ?? 0, 1),
        ];
    }

    public function approve(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => true]);
        unset($this->reviews, $this->stats);
        $this->dispatch('notify', message: 'Review approved.', type: 'success');
    }

    public function reject(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => false]);
        unset($this->reviews, $this->stats);
        $this->dispatch('notify', message: 'Review rejected.', type: 'success');
    }

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
        unset($this->reviews, $this->stats);
        $this->dispatch('notify', message: 'Review deleted.', type: 'success');
    }

    public function bulkApprove(): void
    {
        if (empty($this->selectedIds)) return;
        Review::whereIn('id', $this->selectedIds)->update(['is_approved' => true]);
        $this->selectedIds = [];
        $this->selectAll = false;
        unset($this->reviews, $this->stats);
        $this->dispatch('notify', message: count($this->selectedIds) . ' reviews approved.', type: 'success');
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) return;
        Review::whereIn('id', $this->selectedIds)->delete();
        $count = count($this->selectedIds);
        $this->selectedIds = [];
        $this->selectAll = false;
        unset($this->reviews, $this->stats);
        $this->dispatch('notify', message: "{$count} reviews deleted.", type: 'success');
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->reviews->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedRating(): void { $this->resetPage(); }

    public function render()
    {
        return view('livewire.admin.reviews.review-index');
    }
}
