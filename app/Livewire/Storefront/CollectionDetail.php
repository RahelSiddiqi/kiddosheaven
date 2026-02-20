<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Catalog\Models\Collection;

class CollectionDetail extends Component
{
    use WithPagination;

    public Collection $collection;

    public function mount(string $slug): void
    {
        $this->collection = Collection::where('slug', $slug)
            ->active()
            ->firstOrFail();
    }

    public function render()
    {
        $products = $this->collection->products()
            ->where('is_active', true)
            ->paginate(24);

        return view('livewire.storefront.collection-detail', compact('products'));
    }
}
