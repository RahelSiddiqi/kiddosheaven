<?php

namespace App\Livewire\Admin\Settings;

use App\Domains\Content\Models\Redirect;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('URL Redirects')]
class RedirectIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool   $showForm   = false;
    public ?int   $editingId  = null;
    public string $from_path  = '';
    public string $to_path    = '';
    public int    $type       = 301;
    public bool   $is_active  = true;
    public string $note       = '';

    protected array $rules = [
        'from_path' => 'required|string|max:500',
        'to_path'   => 'required|string|max:500',
        'type'      => 'required|in:301,302',
        'is_active' => 'boolean',
    ];

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'from_path', 'to_path', 'note']);
        $this->type      = 301;
        $this->is_active = true;
        $this->showForm  = true;
    }

    public function openEdit(int $id): void
    {
        $r = Redirect::findOrFail($id);
        $this->editingId = $id;
        $this->from_path = $r->from_path;
        $this->to_path   = $r->to_path;
        $this->type      = $r->type;
        $this->is_active = (bool) $r->is_active;
        $this->note      = $r->note ?? '';
        $this->showForm  = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'from_path' => '/' . ltrim($this->from_path, '/'),
            'to_path'   => $this->to_path,
            'type'      => $this->type,
            'is_active' => $this->is_active,
            'note'      => $this->note ?: null,
        ];

        if ($this->editingId) {
            Redirect::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Redirect updated.');
        } else {
            Redirect::create($data);
            $this->dispatch('toast', type: 'success', message: 'Redirect created.');
        }

        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Redirect::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Redirect deleted.');
    }

    public function toggle(int $id): void
    {
        $r = Redirect::findOrFail($id);
        $r->update(['is_active' => ! $r->is_active]);
    }

    public function render()
    {
        $redirects = Redirect::query()
            ->when($this->search, fn($q) => $q
                ->where('from_path', 'like', "%{$this->search}%")
                ->orWhere('to_path', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('livewire.admin.settings.redirect-index', compact('redirects'));
    }
}
