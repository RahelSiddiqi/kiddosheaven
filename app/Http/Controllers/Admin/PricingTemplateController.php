<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingTemplate;
use App\Models\Category;
use App\Services\PricingTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PricingTemplateController extends Controller
{
    public function __construct(
        private PricingTemplateService $pricingTemplateService
    ) {}

    /**
     * Display all pricing templates
     */
    public function index(): View
    {
        $templates = PricingTemplate::withCount('categories')
            ->orderBy('is_global', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.pricing-templates.index', compact('templates'));
    }

    /**
     * Store a new template
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'strategy_type' => 'required|in:percentage_markup,fixed_markup,tiered,attribute_based',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
            'percentage' => 'required_if:strategy_type,percentage_markup|nullable|numeric|min:0',
            'fixed_amount' => 'required_if:strategy_type,fixed_markup|nullable|numeric|min:0',
            'tiers' => 'required_if:strategy_type,tiered|nullable|array',
            'rules' => 'required_if:strategy_type,attribute_based|nullable|array',
            'default_percentage' => 'nullable|numeric|min:0',
        ]);

        $template = $this->pricingTemplateService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pricing template created successfully',
            'template' => $template,
        ]);
    }

    /**
     * Update existing template
     */
    public function update(Request $request, PricingTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'strategy_type' => 'sometimes|required|in:percentage_markup,fixed_markup,tiered,attribute_based',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
            'percentage' => 'nullable|numeric|min:0',
            'fixed_amount' => 'nullable|numeric|min:0',
            'tiers' => 'nullable|array',
            'rules' => 'nullable|array',
            'default_percentage' => 'nullable|numeric|min:0',
        ]);

        $template = $this->pricingTemplateService->update($template, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Pricing template updated successfully',
            'template' => $template,
        ]);
    }

    /**
     * Delete template
     */
    public function destroy(PricingTemplate $template): JsonResponse
    {
        $this->pricingTemplateService->delete($template);

        return response()->json([
            'success' => true,
            'message' => 'Pricing template deleted successfully',
        ]);
    }

    /**
     * Get template details
     */
    public function show(PricingTemplate $template): JsonResponse
    {
        $template->load('categories');
        $examples = $this->pricingTemplateService->getExampleCalculations($template);

        return response()->json([
            'success' => true,
            'template' => $template,
            'examples' => $examples,
        ]);
    }

    /**
     * Preview price calculation
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'strategy_type' => 'required|in:percentage_markup,fixed_markup,tiered,attribute_based',
            'cost_price' => 'required|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0',
            'fixed_amount' => 'nullable|numeric|min:0',
            'tiers' => 'nullable|array',
            'rules' => 'nullable|array',
            'default_percentage' => 'nullable|numeric|min:0',
            'attributes' => 'nullable|array',
        ]);

        // Create temporary template for calculation
        $tempTemplate = new PricingTemplate([
            'strategy_type' => $validated['strategy_type'],
            'config' => $this->pricingTemplateService->create($validated)->config,
        ]);

        $calculatedPrice = $tempTemplate->calculatePrice(
            $validated['cost_price'],
            $validated['attributes'] ?? []
        );

        return response()->json([
            'success' => true,
            'cost_price' => $validated['cost_price'],
            'calculated_price' => $calculatedPrice,
            'markup' => $calculatedPrice - $validated['cost_price'],
            'markup_percentage' => $validated['cost_price'] > 0
                ? round((($calculatedPrice - $validated['cost_price']) / $validated['cost_price']) * 100, 2)
                : 0,
        ]);
    }

    /**
     * Attach template to categories
     */
    public function attachCategories(Request $request, PricingTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $this->pricingTemplateService->attachToCategories($template, $validated['category_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Template attached to categories successfully',
        ]);
    }

    /**
     * Get templates for dropdown/selection
     */
    public function list(): JsonResponse
    {
        $templates = $this->pricingTemplateService->getActiveTemplates();

        return response()->json([
            'success' => true,
            'templates' => $templates->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'strategy_name' => $t->strategy_name,
                'config_summary' => $t->config_summary,
            ]),
        ]);
    }
}
