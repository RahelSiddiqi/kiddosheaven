<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Catalog\Models\Collection;

class CollectionPage extends Component
{
    public function render()
    {
        $collections = Collection::active()
            ->orderBy('position')
            ->withCount('products')
            ->get();

        return view('livewire.storefront.collection-page', compact('collections'));
    }
}
