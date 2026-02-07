<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function index()
    {
        $brands = $this->brandRepository->with(['products'])->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $this->brandRepository->create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully'
            ]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function update(StoreBrandRequest $request, Brand $brand)
    {
        $data = $request->validated();

        if (!isset($data['slug']) || $data['slug'] !== $brand->slug) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo && Storage::exists('public/' . $brand->logo)) {
                Storage::delete('public/' . $brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $this->brandRepository->update($brand->id, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully'
            ]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        // Check if brand has associated products
        if ($brand->products()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete brand. This brand has ' . $brand->products()->count() . ' associated products.'
                ], 422);
            }
            return redirect()->route('admin.brands.index')
                ->with('error', 'Cannot delete brand. This brand has ' . $brand->products()->count() . ' associated products.');
        }

        // Delete logo if exists
        if ($brand->logo && Storage::exists('public/' . $brand->logo)) {
            Storage::delete('public/' . $brand->logo);
        }

        $this->brandRepository->delete($brand->id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully');
    }

    public function toggleActive(Brand $brand)
    {
        $this->brandRepository->toggleActive($brand->id);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand status updated successfully');
    }
}
