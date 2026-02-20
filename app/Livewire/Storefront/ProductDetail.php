<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Product\Models\Product;
use App\Services\Cart\CartService;

class ProductDetail extends Component
{
    public Product $product;
    public $selectedVariantId = null;
    public $quantity = 1;
    public $selectedImage = null;

    public function mount($slug)
    {
        $this->product = Product::with([
            'category',
            'brand',
            'variants.variantAttributes.attribute',
            'variants.variantAttributes.attributeValue'
        ])->where('slug', $slug)->firstOrFail();

        // Set selected image
        $images = is_array($this->product->images) ? $this->product->images : [];
        $this->selectedImage = $this->product->primary_image ?? ($images[0] ?? null);

        // Set default variant
        if ($this->product->product_type === 'variable') {
            try {
                $defaultVariant = $this->product->activeVariants()->where('is_default', true)->first();
                if ($defaultVariant) {
                    $this->selectedVariantId = $defaultVariant->id;
                }
            } catch (\Exception $e) {
                // No variants relationship, skip
            }
        }
    }

    public function selectImage($image)
    {
        $this->selectedImage = $image;
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $result = app(CartService::class)->addItem($this->product->id, $this->quantity);

        if ($result['success']) {
            $this->dispatch('cart-updated');
            $this->dispatch('notify', message: 'Product added to cart!', type: 'success');
            $this->quantity = 1;
        } else {
            $this->dispatch('notify', message: $result['message'], type: 'error');
        }
    }

    public function render()
    {
        $related = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $reviews = [];
        try {
            $reviews = $this->product->approvedReviews()
                ->with('user')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            // Reviews relationship might not exist
        }

        $variants = collect();
        if ($this->product->product_type === 'variable') {
            try {
                $variants = $this->product->activeVariants()
                    ->with('variantAttributes.attribute', 'variantAttributes.attributeValue')
                    ->get();
            } catch (\Exception $e) {
                // Variants might not exist
            }
        }

        view()->share('page_title', $this->product?->name ?? 'Product');
        view()->share('page_description', $this->product ? strip_tags(substr($this->product->description ?? '', 0, 160)) : '');

        return view('livewire.storefront.product-detail', [
            'related' => $related,
            'reviews' => $reviews,
            'variants' => $variants,
        ]);
    }
}
