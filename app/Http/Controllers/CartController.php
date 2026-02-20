<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->getCart($request);

        return view('shop.cart', [
            'cart' => $cart,
        ]);
    }

    public function add(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $quantity = max(1, (int) $request->input('quantity', 1));
        $variantId = $request->input('variant_id');

        // Variable products require a variant selection
        if ($product->product_type === 'variable' && !$variantId) {
            return redirect()->route('products.show', $product->slug)
                ->with('info', 'Please select your options before adding to cart.');
        }

        // Resolve variant if provided
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->with('variantAttributes.attribute', 'variantAttributes.attributeValue')
                ->first();

            if (!$variant) {
                return redirect()->back()->with('error', 'Selected variant is not available.');
            }
        }

        // Determine price and stock from variant or product
        $price = $variant ? $variant->price : $product->price;
        $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;
        $imagePath = $variant && $variant->image
            ? $variant->image
            : ($product->primary_image ?? (is_array($product->images) && count($product->images) > 0 ? $product->images[0] : null));

        // Stock availability check
        if ($stock < $quantity) {
            return redirect()->back()
                ->with('error', 'Insufficient stock. Only ' . $stock . ' available.');
        }

        $cart = $this->getCart($request);

        // Use composite key: product_id:variant_id to support variants
        $key = $variantId ? "{$product->id}:{$variantId}" : (string) $product->id;

        if (isset($cart['items'][$key])) {
            $newQuantity = $cart['items'][$key]['quantity'] + $quantity;
            if ($stock < $newQuantity) {
                return redirect()->back()
                    ->with('error', 'Insufficient stock. Only ' . $stock . ' available.');
            }
            $cart['items'][$key]['quantity'] = $newQuantity;
        } else {
            // Build variant attributes for display (e.g. "Color: Red, Size: L")
            $variantAttrs = [];
            if ($variant) {
                foreach ($variant->variantAttributes as $va) {
                    $attrName = $va->attribute->name ?? 'Option';
                    $valName = $va->attributeValue->value ?? '';
                    $variantAttrs[$attrName] = $valName;
                }
            }

            $cart['items'][$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId ? (int) $variantId : null,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $price,
                'image' => $imagePath,
                'quantity' => $quantity,
                'variant_attributes' => $variantAttrs,
            ];
        }

        $cart = $this->recalculateCart($cart);
        $this->saveCart($request, $cart);

        return redirect()->route('cart.index')
            ->with('success', 'Added to cart.');
    }

    public function update(Request $request, int $productId)
    {
        $quantity = max(0, (int) $request->input('quantity', 1));
        $variantId = $request->input('variant_id');

        $cart = $this->getCart($request);
        $key = $variantId ? "{$productId}:{$variantId}" : (string) $productId;

        if ($quantity === 0) {
            unset($cart['items'][$key]);
        } elseif (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] = $quantity;
        }

        $cart = $this->recalculateCart($cart);
        $this->saveCart($request, $cart);

        return redirect()->route('cart.index');
    }

    public function remove(Request $request, int $productId)
    {
        $variantId = $request->input('variant_id');

        $cart = $this->getCart($request);
        $key = $variantId ? "{$productId}:{$variantId}" : (string) $productId;

        unset($cart['items'][$key]);

        $cart = $this->recalculateCart($cart);
        $this->saveCart($request, $cart);

        return redirect()->route('cart.index');
    }

    protected function getCart(Request $request): array
    {
        return $request->session()->get('cart', [
            'items' => [],
            'subtotal' => 0,
        ]);
    }

    protected function saveCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    protected function recalculateCart(array $cart): array
    {
        $subtotal = 0;

        foreach ($cart['items'] as &$item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $item['line_total'] = $lineTotal;
            $subtotal += $lineTotal;
        }

        $cart['subtotal'] = $subtotal;
        $cart['total'] = $subtotal;

        return $cart;
    }
}
