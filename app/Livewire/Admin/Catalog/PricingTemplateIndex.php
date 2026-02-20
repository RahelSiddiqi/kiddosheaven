<?php

namespace App\Livewire\Admin\Catalog;

use App\Services\PricingTemplateService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Pricing Templates - Admin')]
class PricingTemplateIndex extends Component
{
    public bool   $showModal     = false;
    public ?int   $editingId     = null;

    // Core form fields
    public string $name          = '';
    public string $description   = '';
    public string $strategyType  = 'percentage_markup';
    public bool   $isActive      = true;
    public bool   $isGlobal      = false;

    // Strategy-specific config fields
    public string $percentage        = '50';  // percentage_markup
    public string $fixedAmount       = '10';  // fixed_markup
    public string $defaultPercentage = '50';  // attribute_based fallback
    public array  $tiers             = [];    // tiered: [{min_cost, percentage}]
    public array  $rules             = [];    // attribute_based: [{conditions:[{attribute,value}], percentage}]

    // Category attach modal
    public bool  $showCategoryModal   = false;
    public ?int  $attachingTemplateId = null;
    public array $selectedCategories  = [];
    public array $allCategories       = [];

    // Preview
    public string $previewCost   = '';
    public ?float $previewResult = null;

    public function mount(): void
    {
        $this->loadFirstTier();
    }

    private function loadFirstTier(): void
    {
        $this->tiers = [['min_cost' => '0', 'percentage' => '50']];
        $this->rules = [['conditions' => [['attribute' => '', 'value' => '']], 'percentage' => '50']];
    }

    private function templateClass(): string
    {
        return class_exists(\App\Domains\Catalog\Models\PricingTemplate::class)
            ? \App\Domains\Catalog\Models\PricingTemplate::class
            : \App\Models\PricingTemplate::class;
    }

    private function categoryClass(): string
    {
        return class_exists(\App\Domains\Catalog\Models\Category::class)
            ? \App\Domains\Catalog\Models\Category::class
            : \App\Models\Category::class;
    }

    public function createModal(): void
    {
        $this->reset(['name', 'description', 'isActive', 'isGlobal', 'editingId', 'previewCost', 'previewResult']);
        $this->strategyType      = 'percentage_markup';
        $this->percentage        = '50';
        $this->fixedAmount       = '10';
        $this->defaultPercentage = '50';
        $this->loadFirstTier();
        $this->isActive          = true;
        $this->showModal         = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $tplClass = $this->templateClass();
        $tpl = $tplClass::findOrFail($id);
        $config = $tpl->config ?? [];

        $this->editingId     = $id;
        $this->name          = $tpl->name;
        $this->description   = $tpl->description ?? '';
        $this->strategyType  = $tpl->strategy_type;
        $this->isActive      = (bool) $tpl->is_active;
        $this->isGlobal      = (bool) $tpl->is_global;
        $this->percentage    = (string) ($config['percentage'] ?? 50);
        $this->fixedAmount   = (string) ($config['fixed_amount'] ?? 10);
        $this->defaultPercentage = (string) ($config['default_percentage'] ?? 50);
        $this->tiers         = !empty($config['tiers']) ? $config['tiers'] : [['min_cost' => '0', 'percentage' => '50']];
        $this->rules         = !empty($config['rules']) ? $config['rules'] : [['conditions' => [['attribute' => '', 'value' => '']], 'percentage' => '50']];
        $this->previewCost   = '';
        $this->previewResult = null;
        $this->showModal     = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function addTier(): void
    {
        $this->tiers[] = ['min_cost' => '', 'percentage' => '50'];
    }

    public function removeTier(int $index): void
    {
        unset($this->tiers[$index]);
        $this->tiers = array_values($this->tiers);
    }

    public function addRule(): void
    {
        $this->rules[] = ['conditions' => [['attribute' => '', 'value' => '']], 'percentage' => '50'];
    }

    public function removeRule(int $index): void
    {
        unset($this->rules[$index]);
        $this->rules = array_values($this->rules);
    }

    public function addCondition(int $ruleIndex): void
    {
        $this->rules[$ruleIndex]['conditions'][] = ['attribute' => '', 'value' => ''];
    }

    public function removeCondition(int $ruleIndex, int $condIndex): void
    {
        unset($this->rules[$ruleIndex]['conditions'][$condIndex]);
        $this->rules[$ruleIndex]['conditions'] = array_values($this->rules[$ruleIndex]['conditions']);
    }

    public function previewPrice(): void
    {
        if (!$this->previewCost || !is_numeric($this->previewCost)) {
            return;
        }

        $config = $this->buildConfig();
        $tplClass = $this->templateClass();

        $temp = new $tplClass(['strategy_type' => $this->strategyType, 'config' => $config]);
        $this->previewResult = $temp->calculatePrice((float) $this->previewCost);
    }

    private function buildConfig(): array
    {
        return match ($this->strategyType) {
            'percentage_markup' => ['percentage' => (float) $this->percentage],
            'fixed_markup'      => ['fixed_amount' => (float) $this->fixedAmount],
            'tiered'            => ['tiers' => array_map(fn($t) => ['min_cost' => (float) $t['min_cost'], 'percentage' => (float) $t['percentage']], $this->tiers)],
            'attribute_based'   => ['rules' => $this->rules, 'default_percentage' => (float) $this->defaultPercentage],
            default             => [],
        };
    }

    public function save(): void
    {
        $nameRule = $this->editingId
            ? 'required|string|max:255|unique:pricing_templates,name,' . $this->editingId
            : 'required|string|max:255|unique:pricing_templates,name';

        $this->validate([
            'name'         => $nameRule,
            'strategyType' => 'required|in:percentage_markup,fixed_markup,tiered,attribute_based',
        ]);

        $data = [
            'name'          => $this->name,
            'description'   => $this->description ?: null,
            'strategy_type' => $this->strategyType,
            'config'        => $this->buildConfig(),
            'is_active'     => $this->isActive,
            'is_global'     => $this->isGlobal,
        ];

        $service = app(PricingTemplateService::class);

        if ($this->editingId) {
            $tplClass = $this->templateClass();
            $tpl = $tplClass::findOrFail($this->editingId);
            $service->update($tpl, $data);
            $this->dispatch('notify', message: 'Template updated.', type: 'success');
        } else {
            $service->create($data);
            $this->dispatch('notify', message: 'Template created.', type: 'success');
        }

        $this->closeModal();
    }

    public function deleteRecord(int $id): void
    {
        $tplClass = $this->templateClass();
        $tpl = $tplClass::withCount('categories')->findOrFail($id);
        app(PricingTemplateService::class)->delete($tpl);
        $this->dispatch('notify', message: 'Template deleted.', type: 'success');
    }

    public function openCategoryModal(int $id): void
    {
        $tplClass = $this->templateClass();
        $tpl = $tplClass::with('categories')->findOrFail($id);
        $this->attachingTemplateId = $id;
        $this->selectedCategories  = $tpl->categories->pluck('id')->map(fn($i) => (string) $i)->toArray();

        $catClass = $this->categoryClass();
        $this->allCategories = $catClass::orderBy('name')->get(['id', 'name'])->toArray();
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal   = false;
        $this->attachingTemplateId = null;
    }

    public function saveCategories(): void
    {
        $tplClass = $this->templateClass();
        $tpl = $tplClass::findOrFail($this->attachingTemplateId);
        app(PricingTemplateService::class)->attachToCategories($tpl, array_map('intval', $this->selectedCategories));
        $this->showCategoryModal   = false;
        $this->attachingTemplateId = null;
        $this->dispatch('notify', message: 'Categories assigned.', type: 'success');
    }

    public function render()
    {
        $tplClass = $this->templateClass();

        $templates = $tplClass::withCount('categories')
            ->orderByDesc('is_global')
            ->orderBy('name')
            ->get();

        $stats = [
            'total'  => $templates->count(),
            'active' => $templates->where('is_active', true)->count(),
            'global' => $templates->where('is_global', true)->count(),
        ];

        return view('livewire.admin.catalog.pricing-template-index', compact('templates', 'stats'));
    }
}
