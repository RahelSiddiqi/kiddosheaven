<?php

namespace App\Livewire\Admin\Role;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('admin.layouts.app')]
#[Title('Roles - Admin')]
class RoleIndex extends Component
{
    // Modal state
    public bool  $showModal  = false;
    public ?int  $editingId  = null;

    // Form fields
    public string $name        = '';
    public string $description = '';
    public bool   $isDefault   = false;
    public array  $selectedPermissions = [];  // array of permission IDs

    public function updatedName(): void
    {
        // Slug is auto-generated on save, optionally show preview here
    }

    public function createModal(): void
    {
        $this->reset(['name', 'description', 'isDefault', 'selectedPermissions']);
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingId           = $id;
        $this->name                = $role->name;
        $this->description         = $role->description ?? '';
        $this->isDefault           = (bool) $role->is_default;
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->showModal           = true;
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
            ? 'required|string|max:255|unique:roles,name,' . $this->editingId
            : 'required|string|max:255|unique:roles,name';

        $this->validate([
            'name'                  => $nameRule,
            'selectedPermissions'   => 'array',
            'selectedPermissions.*' => 'exists:permissions,id',
        ]);

        $data = [
            'name'        => $this->name,
            'slug'        => Str::slug($this->name),
            'description' => $this->description ?: null,
            'is_default'  => $this->isDefault,
        ];

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            $role->update($data);
            $role->permissions()->sync(array_map('intval', $this->selectedPermissions));
            $this->dispatch('notify', message: 'Role updated.', type: 'success');
        } else {
            $role = Role::create($data);
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->attach(array_map('intval', $this->selectedPermissions));
            }
            $this->dispatch('notify', message: 'Role created.', type: 'success');
        }

        $this->closeModal();
    }

    public function deleteRecord(int $id): void
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            $this->dispatch('notify', message: 'Cannot delete role: it has ' . $role->users_count . ' assigned users.', type: 'error');
            return;
        }

        $role->permissions()->detach();
        $role->delete();
        $this->dispatch('notify', message: 'Role deleted.', type: 'success');
    }

    public function render()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        $permissions = Permission::getGroupedPermissions(); // grouped by 'group' key

        return view('livewire.admin.role.role-index', compact('roles', 'permissions'));
    }
}
