<?php

namespace App\Livewire\Admin\Permission;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('admin.layouts.app')]
#[Title('Permissions - Admin')]
class PermissionIndex extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $group = '';

    // Create/Edit modal
    public bool   $showModal  = false;
    public ?int   $editingId  = null;
    public string $name        = '';
    public string $permGroup   = '';  // group field (renamed to avoid conflict with $group url param)
    public string $description = '';

    // Assign-to-role modal
    public bool   $showAssignModal = false;
    public int    $assignRoleId    = 0;
    public array  $assignedPermIds = [];

    public function updatedSearch(): void
    {
        // No pagination needed - small dataset
    }

    public function createModal(): void
    {
        $this->reset(['name', 'permGroup', 'description']);
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $p = Permission::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $p->name;
        $this->permGroup   = $p->group ?? '';
        $this->description = $p->description ?? '';
        $this->showModal   = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $nameRule = $this->editingId
            ? 'required|string|max:255|unique:permissions,name,' . $this->editingId
            : 'required|string|max:255|unique:permissions,name';

        $this->validate([
            'name'      => $nameRule,
            'permGroup' => 'nullable|string|max:100',
        ]);

        $data = [
            'name'        => $this->name,
            'slug'        => Str::slug($this->name),
            'group'       => $this->permGroup ?: 'general',
            'description' => $this->description ?: null,
        ];

        if ($this->editingId) {
            Permission::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Permission updated.', type: 'success');
        } else {
            Permission::create($data);
            $this->dispatch('notify', message: 'Permission created.', type: 'success');
        }

        $this->closeModal();
    }

    public function deleteRecord(int $id): void
    {
        $permission = Permission::findOrFail($id);
        $permission->roles()->detach();
        $permission->delete();
        $this->dispatch('notify', message: 'Permission deleted.', type: 'success');
    }

    public function generate(): void
    {
        $methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $areas = [
            'dashboard', 'categories', 'products', 'brands', 'orders', 'customers',
            'inventory', 'coupons', 'flash-sales', 'cms-pages', 'reviews',
            'expenses', 'partners', 'investments', 'reports', 'settings', 'roles', 'permissions',
            'users', 'pricing-templates', 'loyalty', 'attributes',
        ];
        $count = 0;

        foreach ($areas as $area) {
            foreach ($methods as $method) {
                $slug = $area . '.' . $method;
                $name = ucfirst($method) . ' ' . ucfirst(str_replace('-', ' ', $area));

                if (!Permission::where('slug', $slug)->exists()) {
                    Permission::create([
                        'name'  => $name,
                        'slug'  => $slug,
                        'group' => $area,
                    ]);
                    $count++;
                }
            }
        }

        $this->dispatch('notify', message: "Generated {$count} new permissions.", type: 'success');
    }

    public function openAssignModal(int $roleId): void
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->assignRoleId    = $roleId;
        $this->assignedPermIds = $role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->assignRoleId    = 0;
        $this->assignedPermIds = [];
    }

    public function saveAssign(): void
    {
        $role = Role::findOrFail($this->assignRoleId);
        $role->permissions()->sync(array_map('intval', $this->assignedPermIds));
        $this->showAssignModal = false;
        $this->dispatch('notify', message: 'Permissions assigned to role.', type: 'success');
    }

    public function render()
    {
        $query = Permission::query()
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            }))
            ->when($this->group, fn($q) => $q->where('group', $this->group))
            ->orderBy('group')
            ->orderBy('name');

        $permissions = $query->get()->groupBy('group');
        $groups      = Permission::distinct()->pluck('group')->filter()->sort()->values();
        $roles       = Role::orderBy('name')->get(['id', 'name']);
        $allPermissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('livewire.admin.permission.permission-index', compact('permissions', 'groups', 'roles', 'allPermissions'));
    }
}
