<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Domains\Catalog\Models\Category;

class Navigation extends Component
{
    public $cartCount = 0;
    public $searchQuery = '';
    public $isMobileMenuOpen = false;

    public function mount()
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $cart = session('cart', []);
        $items = $cart['items'] ?? [];
        $this->cartCount = array_sum(array_column($items, 'quantity'));
    }

    public function search()
    {
        if (empty($this->searchQuery)) {
            return;
        }

        return $this->redirect(route('catalog', ['q' => $this->searchQuery]), navigate: true);
    }

    public function toggleMobileMenu()
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public function render()
    {
        $categories = cache()->remember('nav-categories', 3600, function() {
            return Category::whereNull('parent_id')
                ->with('children')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return view('livewire.storefront.navigation', compact('categories'));
    }
}
