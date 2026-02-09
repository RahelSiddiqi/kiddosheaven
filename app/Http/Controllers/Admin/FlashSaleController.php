<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FlashSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = FlashSale::with('products');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $flashSales = $query->latest()->paginate(10);

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();

        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.discount_price' => 'required|numeric|min:0',
            'products.*.discount_type' => 'required|in:percentage,fixed',
            'products.*.limit_per_customer' => 'nullable|integer|min:1',
        ]);

        $flashSale = FlashSale::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'subtitle' => $validated['subtitle'] ?? null,
            'background_color' => $validated['background_color'] ?? '#ff6b6b',
            'text_color' => $validated['text_color'] ?? '#ffffff',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'draft',
        ]);

        foreach ($validated['products'] as $product) {
            $flashSale->products()->attach($product['id'], [
                'discount_price' => $product['discount_price'],
                'discount_type' => $product['discount_type'],
                'limit_per_customer' => $product['limit_per_customer'] ?? null,
            ]);
        }

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale created successfully.');
    }

    public function show(FlashSale $flashSale)
    {
        $flashSale->load(['products' => function ($query) {
            $query->withPivot('discount_price', 'discount_type', 'limit_per_customer', 'sold_count');
        }]);

        return view('admin.flash-sales.show', compact('flashSale'));
    }

    public function edit(FlashSale $flashSale)
    {
        $flashSale->load(['products' => function ($query) {
            $query->withPivot('discount_price', 'discount_type', 'limit_per_customer');
        }]);

        $products = Product::where('is_active', true)->get();

        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.discount_price' => 'required|numeric|min:0',
            'products.*.discount_type' => 'required|in:percentage,fixed',
            'products.*.limit_per_customer' => 'nullable|integer|min:1',
        ]);

        $flashSale->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'subtitle' => $validated['subtitle'] ?? null,
            'background_color' => $validated['background_color'] ?? '#ff6b6b',
            'text_color' => $validated['text_color'] ?? '#ffffff',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        $flashSale->products()->detach();

        foreach ($validated['products'] as $product) {
            $flashSale->products()->attach($product['id'], [
                'discount_price' => $product['discount_price'],
                'discount_type' => $product['discount_type'],
                'limit_per_customer' => $product['limit_per_customer'] ?? null,
            ]);
        }

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale updated successfully.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->products()->detach();
        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale deleted successfully.');
    }

    public function publish(FlashSale $flashSale)
    {
        $flashSale->update(['status' => 'active']);

        return back()->with('success', 'Flash sale published.');
    }

    public function unpublish(FlashSale $flashSale)
    {
        $flashSale->update(['status' => 'inactive']);

        return back()->with('success', 'Flash sale unpublished.');
    }
}
