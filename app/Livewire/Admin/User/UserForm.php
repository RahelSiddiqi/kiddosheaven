<?php

namespace App\Livewire\Admin\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('admin.layouts.app')]
#[Title('User Form - Admin')]
class UserForm extends Component
{
    #[Locked]
    public ?int $userId = null;

    public string  $name     = '';
    public string  $email    = '';
    public string  $phone    = '';
    public ?int    $roleId   = null;
    public bool    $isAdmin  = false;
    public bool    $isActive = true;
    public string  $password        = '';
    public string  $passwordConfirm = '';

    // UI data
    public array $roles = [];

    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
        $this->roles  = Role::orderBy('name')->get(['id', 'name'])->toArray();

        if ($userId) {
            $user = User::findOrFail($userId);
            $this->name     = $user->name;
            $this->email    = $user->email;
            $this->phone    = $user->phone ?? '';
            $this->roleId   = $user->role_id;
            $this->isAdmin  = $user->is_admin;
            $this->isActive = $user->is_active;
        }
    }

    public function save(): void
    {
        $rules = [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255',
                $this->userId
                    ? \Illuminate\Validation\Rule::unique('users', 'email')->ignore($this->userId)
                    : 'unique:users,email',
            ],
            'phone'   => ['nullable', 'string', 'max:30'],
            'roleId'  => ['nullable', 'exists:roles,id'],
        ];

        if (!$this->userId) {
            $rules['password']        = ['required', Password::min(8)];
            $rules['passwordConfirm'] = ['required', 'same:password'];
        } else {
            $rules['password']        = ['nullable', Password::min(8)];
            $rules['passwordConfirm'] = ['nullable', 'same:password'];
        }

        $this->validate($rules, [
            'email.unique'           => 'This email is already in use.',
            'password.required'      => 'Password is required for new users.',
            'passwordConfirm.same'   => 'Passwords do not match.',
        ]);

        $data = [
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone ?: null,
            'role_id'   => $this->roleId,
            'is_admin'  => $this->isAdmin,
            'is_active' => $this->isActive,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::findOrFail($this->userId)->update($data);
            $this->dispatch('notify', message: 'User updated successfully.', type: 'success');
        } else {
            User::create($data);
            $this->dispatch('notify', message: 'User created successfully.', type: 'success');
        }

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.user.user-form');
    }
}
