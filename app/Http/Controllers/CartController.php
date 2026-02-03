<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

        $cart = $this->getCart($request);

        $key = (string) $product->id;
        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] += $quantity;
        } else {
            $cart['items'][$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image_path' => $product->image_path,
                'quantity' => $quantity,
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

        $cart = $this->getCart($request);
        $key = (string) $productId;

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
        $cart = $this->getCart($request);
        $key = (string) $productId;

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

        return $cart;
    }
}
