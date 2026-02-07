<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $stockFilter = $request->get('filter', 'all');

        $query = Product::query();

        if ($stockFilter === 'low') {
            $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
        } elseif ($stockFilter === 'out') {
            $query->where('stock_quantity', 0);
        }

        $products = $query->latest()->paginate(20);

        return view('admin.inventory.index', compact('products', 'stockFilter'));
    }

    public function alerts()
    {
        $lowStockProducts = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->limit(10)->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->get();

        // Combine both into a single alerts collection
        $alerts = $lowStockProducts->merge($outOfStockProducts);

        return view('admin.inventory.alerts', compact('alerts'));
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'action' => ['nullable', 'in:add,set,deduct']
        ]);

        $product = Product::findOrFail($request->product_id);
        $action = $request->action ?? 'set';

        switch ($action) {
            case 'add':
                $newQuantity = $product->stock_quantity + $request->quantity;
                break;
            case 'deduct':
                $newQuantity = max(0, $product->stock_quantity - $request->quantity);
                break;
            default:
                $newQuantity = $request->quantity;
                break;
        }

        $product->update(['stock_quantity' => $newQuantity]);

        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
    }
}
