<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Product\Models\Product;

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

    public function addToCart()
    {
        $cart = session('cart', ['items' => []]);

        $key = $this->product->id . ($this->selectedVariantId ? '-' . $this->selectedVariantId : '');

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] += $this->quantity;
        } else {
            $cart['items'][$key] = [
                'product_id' => $this->product->id,
                'variant_id' => $this->selectedVariantId,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => $this->product->price,
                'image' => $this->product->primary_image ?? ($this->product->images[0] ?? null),
                'quantity' => $this->quantity,
            ];
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated');

        session()->flash('cart-success', 'Product added to cart!');
        $this->quantity = 1;
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

        return view('livewire.storefront.product-detail', [
            'related' => $related,
            'reviews' => $reviews,
            'variants' => $variants,
        ]);
    }
}
