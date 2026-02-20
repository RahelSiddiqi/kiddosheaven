<?php

namespace App\Livewire\Admin\Product;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Manage Attribute Values - Admin')]
class AttributeValueManager extends Component
{
    #[Locked]
    public int $attributeId;

    public string $attribute_name = '';
    public string $attribute_type = '';

    // Quick-add form
    public string $newValue        = '';
    public string $newDisplayValue = '';
    public string $newColorCode    = '#000000';

    // Edit modal state
    public bool   $showEditModal    = false;
    public ?int   $editingValueId   = null;
    public string $editValue        = '';
    public string $editDisplayValue = '';
    public string $editColorCode    = '#000000';
    public string $editPriceModifier= '';

    // Bulk import
    public bool   $showBulkModal = false;
    public string $bulkText      = '';

    public function mount(int $attributeId): void
    {
        $this->attributeId = $attributeId;
        $this->loadAttribute();
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function attributeClass(): string
    {
        return class_exists(\App\Domains\Product\Models\ProductAttribute::class)
            ? \App\Domains\Product\Models\ProductAttribute::class
            : \App\Models\ProductAttribute::class;
    }

    private function valueClass(): string
    {
        return class_exists(\App\Domains\Product\Models\ProductAttributeValue::class)
            ? \App\Domains\Product\Models\ProductAttributeValue::class
            : \App\Models\ProductAttributeValue::class;
    }

    private function loadAttribute(): void
    {
        $attrClass = $this->attributeClass();
        $attr = $attrClass::findOrFail($this->attributeId);
        $this->attribute_name = $attr->name;
        $this->attribute_type = $attr->type;
    }

    // ── Add Value ────────────────────────────────────────────────

    public function addValue(): void
    {
        $this->validate([
            'newValue' => 'required|string|max:255',
        ]);

        $valueClass = $this->valueClass();

        $valueClass::create([
            'product_attribute_id' => $this->attributeId,
            'value'                => $this->newValue,
            'display_value'        => $this->newDisplayValue ?: null,
            'color_code'           => $this->attribute_type === 'color' ? ($this->newColorCode ?: null) : null,
            'sort_order'           => $valueClass::where('product_attribute_id', $this->attributeId)->max('sort_order') + 1,
        ]);

        $this->reset(['newValue', 'newDisplayValue']);
        $this->newColorCode = '#000000';
        $this->resetValidation();
        $this->dispatch('notify', message: 'Value added.', type: 'success');
    }

    // ── Edit Value ───────────────────────────────────────────────

    public function editModal(int $valueId): void
    {
        $valueClass = $this->valueClass();
        $val = $valueClass::findOrFail($valueId);

        $this->editingValueId    = $valueId;
        $this->editValue         = $val->value;
        $this->editDisplayValue  = $val->display_value ?? '';
        $this->editColorCode     = $val->color_code ?? '#000000';
        $this->editPriceModifier = (string) ($val->price_modifier ?? '');
        $this->showEditModal     = true;
        $this->resetValidation();
    }

    public function closeEditModal(): void
    {
        $this->showEditModal  = false;
        $this->editingValueId = null;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editValue'         => 'required|string|max:255',
            'editPriceModifier' => 'nullable|numeric',
        ]);

        $valueClass = $this->valueClass();

        $valueClass::findOrFail($this->editingValueId)->update([
            'value'          => $this->editValue,
            'display_value'  => $this->editDisplayValue ?: null,
            'color_code'     => $this->attribute_type === 'color' ? ($this->editColorCode ?: null) : null,
            'price_modifier' => $this->editPriceModifier !== '' ? (float) $this->editPriceModifier : null,
        ]);

        $this->closeEditModal();
        $this->dispatch('notify', message: 'Value updated.', type: 'success');
    }

    // ── Delete Value ─────────────────────────────────────────────

    public function deleteValue(int $valueId): void
    {
        $valueClass = $this->valueClass();
        $valueClass::findOrFail($valueId)->delete();
        $this->dispatch('notify', message: 'Value deleted.', type: 'success');
    }

    // ── Bulk Import ──────────────────────────────────────────────

    public function openBulkModal(): void
    {
        $this->bulkText      = '';
        $this->showBulkModal = true;
    }

    public function closeBulkModal(): void
    {
        $this->showBulkModal = false;
        $this->bulkText      = '';
    }

    public function bulkImport(): void
    {
        $this->validate(['bulkText' => 'required|string']);

        $valueClass = $this->valueClass();

        $lines = array_filter(array_map('trim', explode("\n", $this->bulkText)));
        $maxSort = $valueClass::where('product_attribute_id', $this->attributeId)->max('sort_order') ?? 0;
        $imported = 0;

        foreach (array_slice($lines, 0, 500) as $i => $line) {
            [$value, $display] = array_pad(explode('|', $line, 2), 2, null);
            if (!$value) continue;

            $valueClass::firstOrCreate(
                ['product_attribute_id' => $this->attributeId, 'value' => trim($value)],
                ['display_value' => $display ? trim($display) : null, 'sort_order' => $maxSort + $i + 1]
            );
            $imported++;
        }

        $this->closeBulkModal();
        $this->dispatch('notify', message: "Bulk import completed. {$imported} values processed.", type: 'success');
    }

    // ── Render ───────────────────────────────────────────────────

    public function render()
    {
        $attrClass = $this->attributeClass();
        $valueClass = $this->valueClass();

        $attribute = $attrClass::withCount('values')->findOrFail($this->attributeId);
        $values    = $valueClass::where('product_attribute_id', $this->attributeId)
            ->orderBy('sort_order')
            ->orderBy('value')
            ->get();

        return view('livewire.admin.product.attribute-value-manager', compact('attribute', 'values'));
    }
}
