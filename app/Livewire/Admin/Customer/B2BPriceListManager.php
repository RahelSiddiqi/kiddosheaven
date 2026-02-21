<?php

namespace App\Livewire\Admin\Customer;

use App\Domains\B2B\Models\CustomerGroup;
use App\Domains\B2B\Models\PriceList;
use App\Domains\B2B\Models\PriceListItem;
use App\Domains\Product\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('B2B Price Lists')]
class B2BPriceListManager extends Component
{
    public bool   $showGroupForm = false;
    public bool   $showListForm  = false;
    public bool   $showItemForm  = false;

    public ?int   $editingGroupId = null;
    public ?int   $editingListId  = null;
    public ?int   $editingItemId  = null;
    public ?int   $activeGroupId  = null;
    public ?int   $activeListId   = null;

    // Group form
    public string $group_name             = '';
    public string $group_slug             = '';
    public float  $group_discount         = 0;
    public bool   $group_is_default       = false;
    public bool   $group_tax_exempt       = false;
    public string $group_min_order        = '';

    // List form
    public string $list_name        = '';
    public string $list_description = '';
    public string $list_starts_at   = '';
    public string $list_ends_at     = '';
    public bool   $list_is_active   = true;

    // Item form
    public ?int   $item_product_id       = null;
    public string $item_price            = '';
    public string $item_compare_at_price = '';
    public int    $item_min_qty          = 1;

    // ── Groups ─────────────────────────────────────────────────────

    public function openCreateGroup(): void
    {
        $this->reset(['editingGroupId', 'group_name', 'group_slug', 'group_min_order']);
        $this->group_discount   = 0;
        $this->group_is_default = false;
        $this->group_tax_exempt = false;
        $this->showGroupForm    = true;
    }

    public function openEditGroup(int $id): void
    {
        $g = CustomerGroup::findOrFail($id);
        $this->editingGroupId    = $id;
        $this->group_name        = $g->name;
        $this->group_slug        = $g->slug;
        $this->group_discount    = (float) $g->discount_percent;
        $this->group_is_default  = (bool) $g->is_default;
        $this->group_tax_exempt  = (bool) $g->tax_exempt;
        $this->group_min_order   = $g->min_order_amount ?? '';
        $this->showGroupForm     = true;
    }

    public function saveGroup(): void
    {
        $this->validate([
            'group_name' => 'required|string|max:100',
            'group_discount' => 'numeric|min:0|max:100',
        ]);

        $data = [
            'name'             => $this->group_name,
            'slug'             => \Illuminate\Support\Str::slug($this->group_name),
            'discount_percent' => $this->group_discount,
            'is_default'       => $this->group_is_default,
            'tax_exempt'       => $this->group_tax_exempt,
            'min_order_amount' => $this->group_min_order ?: null,
            'is_active'        => true,
        ];

        if ($this->editingGroupId) {
            CustomerGroup::findOrFail($this->editingGroupId)->update($data);
        } else {
            CustomerGroup::create($data);
        }

        $this->showGroupForm = false;
        $this->dispatch('toast', type: 'success', message: 'Customer group saved.');
    }

    public function deleteGroup(int $id): void
    {
        CustomerGroup::findOrFail($id)->delete();
    }

    // ── Price Lists ────────────────────────────────────────────────

    public function selectGroup(int $id): void
    {
        $this->activeGroupId = $id;
        $this->activeListId  = null;
    }

    public function openCreateList(int $groupId): void
    {
        $this->activeGroupId    = $groupId;
        $this->editingListId    = null;
        $this->list_name        = '';
        $this->list_description = '';
        $this->list_starts_at   = '';
        $this->list_ends_at     = '';
        $this->list_is_active   = true;
        $this->showListForm     = true;
    }

    public function saveList(): void
    {
        $this->validate(['list_name' => 'required|string|max:100']);

        $data = [
            'customer_group_id' => $this->activeGroupId,
            'name'              => $this->list_name,
            'description'       => $this->list_description ?: null,
            'is_active'         => $this->list_is_active,
            'starts_at'         => $this->list_starts_at ?: null,
            'ends_at'           => $this->list_ends_at ?: null,
        ];

        if ($this->editingListId) {
            PriceList::findOrFail($this->editingListId)->update($data);
        } else {
            PriceList::create($data);
        }

        $this->showListForm = false;
    }

    // ── Price Items ────────────────────────────────────────────────

    public function selectList(int $id): void
    {
        $this->activeListId = $id;
    }

    public function openAddItem(int $listId): void
    {
        $this->activeListId          = $listId;
        $this->editingItemId         = null;
        $this->item_product_id       = null;
        $this->item_price            = '';
        $this->item_compare_at_price = '';
        $this->item_min_qty          = 1;
        $this->showItemForm          = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'item_product_id' => 'required|exists:products,id',
            'item_price'       => 'required|numeric|min:0',
        ]);

        PriceListItem::updateOrCreate(
            ['price_list_id' => $this->activeListId, 'product_id' => $this->item_product_id, 'product_variant_id' => null],
            ['price' => $this->item_price, 'compare_at_price' => $this->item_compare_at_price ?: null, 'min_qty' => $this->item_min_qty]
        );

        $this->showItemForm = false;
        $this->dispatch('toast', type: 'success', message: 'Price item saved.');
    }

    public function deleteItem(int $id): void
    {
        PriceListItem::findOrFail($id)->delete();
    }

    public function render()
    {
        $groups = CustomerGroup::withCount('customers')->get();

        $lists = $this->activeGroupId
            ? PriceList::where('customer_group_id', $this->activeGroupId)->withCount('items')->get()
            : collect();

        $items = $this->activeListId
            ? PriceListItem::where('price_list_id', $this->activeListId)->with('product')->get()
            : collect();

        $products = Product::select('id', 'name', 'sku', 'price')->orderBy('name')->limit(300)->get();
        $activeGroup = $this->activeGroupId ? $groups->firstWhere('id', $this->activeGroupId) : null;
        $activeList  = $this->activeListId ? $lists->firstWhere('id', $this->activeListId) : null;

        return view('livewire.admin.customer.b2b-price-list-manager',
            compact('groups', 'lists', 'items', 'products', 'activeGroup', 'activeList')
        );
    }
}
