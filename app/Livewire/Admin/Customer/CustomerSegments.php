<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Domains\Customer\Models\CustomerSegment;

#[Layout('admin.layouts.app')]
#[Title('Customer Segments - Admin')]
class CustomerSegments extends Component
{
    public bool   $showForm    = false;
    public ?int   $editingId   = null;
    public string $name        = '';
    public string $description = '';
    public string $conditionMatch = 'all'; // all | any

    protected function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:500'],
            'conditionMatch' => ['in:all,any'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $segment = CustomerSegment::findOrFail($id);
        $this->editingId      = $id;
        $this->name           = $segment->name;
        $this->description    = $segment->description ?? '';
        $this->conditionMatch = $segment->condition_match ?? 'all';
        $this->showForm       = true;
    }

    public function save(): void
    {
        $this->validate();

        $siteId = optional(app('currentSite'))->id ?? null;

        $data = [
            'name'            => $this->name,
            'description'     => $this->description ?: null,
            'condition_match' => $this->conditionMatch,
            'conditions'      => [],
        ];

        if ($siteId) {
            $data['site_id'] = $siteId;
        }

        if ($this->editingId) {
            CustomerSegment::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Segment updated.', type: 'success');
        } else {
            CustomerSegment::create($data);
            $this->dispatch('notify', message: 'Segment created.', type: 'success');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        CustomerSegment::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Segment deleted.', type: 'info');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId      = null;
        $this->name           = '';
        $this->description    = '';
        $this->conditionMatch = 'all';
        $this->showForm       = false;
        $this->resetValidation();
    }

    public function render()
    {
        $segments = CustomerSegment::withCount('users')->latest()->get();

        return view('livewire.admin.customer.customer-segments', compact('segments'));
    }
}
