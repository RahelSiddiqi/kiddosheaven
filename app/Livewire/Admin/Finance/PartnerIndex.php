<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Partners - Admin')]
class PartnerIndex extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $typeFilter = '';

    // ── Partner Modal State ───────────────────────────────────────────
    public bool $showModal = false;
    #[Locked]
    public ?int $editingId = null;

    // Partner form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $type = 'supplier';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $country = '';
    public string $postalCode = '';
    public string $taxNumber = '';
    public string $bankName = '';
    public string $bankAccountNumber = '';
    public string $bankRoutingNumber = '';
    public ?float $commissionRate = null;
    public string $notes = '';
    public string $status = 'active';

    // ── Detail View State ─────────────────────────────────────────────
    public ?int $viewingPartnerId = null;
    public string $detailTab = 'info';

    // ── Payment Modal State ───────────────────────────────────────────
    public bool $showPaymentModal = false;
    #[Locked]
    public ?int $editingPaymentId = null;
    public ?int $paymentPartnerId = null;

    // Payment form fields
    public ?float $paymentAmount = null;
    public ?string $paymentDate = null;
    public string $paymentReference = '';
    public string $paymentStatus = 'completed';
    public string $paymentNotes = '';

    // ── Lifecycle ─────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    // ── Model Helpers ─────────────────────────────────────────────────

    private function partnerClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\Partner::class)
            ? \App\Domains\Finance\Models\Partner::class
            : \App\Models\Partner::class;
    }

    private function paymentClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\PartnerPayment::class)
            ? \App\Domains\Finance\Models\PartnerPayment::class
            : \App\Models\PartnerPayment::class;
    }

    private function calculationClass(): string
    {
        return class_exists(\App\Domains\Accounting\Models\PartnerCalculation::class)
            ? \App\Domains\Accounting\Models\PartnerCalculation::class
            : \App\Models\PartnerCalculation::class;
    }

    // ── Partner CRUD Modal ────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset([
            'name', 'email', 'phone', 'address', 'city', 'state', 'country',
            'postalCode', 'taxNumber', 'bankName', 'bankAccountNumber',
            'bankRoutingNumber', 'commissionRate', 'notes'
        ]);
        $this->type = 'supplier';
        $this->status = 'active';
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $modelClass = $this->partnerClass();
        $partner = $modelClass::findOrFail($id);

        $this->editingId = $id;
        $this->name = $partner->name ?? '';
        $this->email = $partner->email ?? '';
        $this->phone = $partner->phone ?? '';
        $this->type = $partner->type ?? 'supplier';
        $this->address = $partner->address ?? '';
        $this->city = $partner->city ?? '';
        $this->state = $partner->state ?? '';
        $this->country = $partner->country ?? '';
        $this->postalCode = $partner->postal_code ?? '';
        $this->taxNumber = $partner->tax_number ?? '';
        $this->bankName = $partner->bank_name ?? '';
        $this->bankAccountNumber = $partner->bank_account_number ?? '';
        $this->bankRoutingNumber = $partner->bank_routing_number ?? '';
        $this->commissionRate = $partner->commission_rate;
        $this->notes = $partner->notes ?? '';
        $this->status = $partner->status ?? 'active';

        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $modelClass = $this->partnerClass();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:supplier,manufacturer,distributor,affiliate,franchise,employee,service_provider,reseller',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postalCode' => 'nullable|string|max:20',
            'taxNumber' => 'nullable|string|max:50',
            'bankName' => 'nullable|string|max:255',
            'bankAccountNumber' => 'nullable|string|max:50',
            'bankRoutingNumber' => 'nullable|string|max:50',
            'commissionRate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended,blocked',
        ];

        if ($this->editingId) {
            $rules['email'] = 'required|email|max:255|unique:partners,email,' . $this->editingId;
        } else {
            $rules['email'] = 'required|email|max:255|unique:partners,email';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'state' => $this->state ?: null,
            'country' => $this->country ?: null,
            'postal_code' => $this->postalCode ?: null,
            'tax_number' => $this->taxNumber ?: null,
            'bank_name' => $this->bankName ?: null,
            'bank_account_number' => $this->bankAccountNumber ?: null,
            'bank_routing_number' => $this->bankRoutingNumber ?: null,
            'commission_rate' => $this->commissionRate,
            'notes' => $this->notes ?: null,
            'status' => $this->status,
            'slug' => \Illuminate\Support\Str::slug($this->name),
        ];

        if ($this->editingId) {
            $modelClass::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Partner updated successfully.', type: 'success');
        } else {
            $modelClass::create($data);
            $this->dispatch('notify', message: 'Partner created successfully.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $modelClass = $this->partnerClass();
        $partner = $modelClass::findOrFail($id);
        $partner->update(['status' => $newStatus]);
        $this->dispatch('notify', message: 'Partner status updated.', type: 'success');
    }

    public function deleteRecord(int $id): void
    {
        $modelClass = $this->partnerClass();
        $partner = $modelClass::withCount(['payments', 'calculations'])->findOrFail($id);

        if (($partner->payments_count ?? 0) > 0) {
            $this->dispatch('notify', message: 'Cannot delete: partner has payments.', type: 'error');
            return;
        }

        if (($partner->calculations_count ?? 0) > 0) {
            $this->dispatch('notify', message: 'Cannot delete: partner has calculations.', type: 'error');
            return;
        }

        $partner->delete();

        // Close detail view if viewing deleted partner
        if ($this->viewingPartnerId === $id) {
            $this->viewingPartnerId = null;
        }

        $this->dispatch('notify', message: 'Partner deleted.', type: 'success');
    }

    // ── Detail View ───────────────────────────────────────────────────

    public function openDetail(int $id): void
    {
        $this->viewingPartnerId = $id;
        $this->detailTab = 'info';
    }

    public function closeDetail(): void
    {
        $this->viewingPartnerId = null;
    }

    public function setDetailTab(string $tab): void
    {
        $this->detailTab = $tab;
    }

    #[Computed]
    public function viewingPartner()
    {
        if (!$this->viewingPartnerId) {
            return null;
        }

        $modelClass = $this->partnerClass();
        return $modelClass::with(['payments', 'calculations', 'purchaseBatches'])
            ->withCount(['payments', 'calculations'])
            ->find($this->viewingPartnerId);
    }

    // ── Payment Modal ─────────────────────────────────────────────────

    public function createPaymentModal(int $partnerId): void
    {
        $this->reset(['paymentAmount', 'paymentReference', 'paymentNotes']);
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentStatus = 'completed';
        $this->paymentPartnerId = $partnerId;
        $this->editingPaymentId = null;
        $this->showPaymentModal = true;
        $this->resetValidation();
    }

    public function editPaymentModal(int $paymentId): void
    {
        $paymentClass = $this->paymentClass();
        $payment = $paymentClass::findOrFail($paymentId);

        $this->editingPaymentId = $paymentId;
        $this->paymentPartnerId = $payment->partner_id;
        $this->paymentAmount = (float) $payment->amount;
        $this->paymentDate = $payment->payment_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->paymentReference = $payment->reference ?? '';
        $this->paymentStatus = $payment->status ?? 'completed';
        $this->paymentNotes = $payment->notes ?? '';
        $this->showPaymentModal = true;
        $this->resetValidation();
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->editingPaymentId = null;
        $this->paymentPartnerId = null;
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentDate' => 'required|date',
            'paymentReference' => 'nullable|string|max:255',
            'paymentStatus' => 'required|in:completed,pending,cancelled',
            'paymentNotes' => 'nullable|string',
            'paymentPartnerId' => 'required|exists:partners,id',
        ]);

        $paymentClass = $this->paymentClass();

        $data = [
            'partner_id' => $this->paymentPartnerId,
            'amount' => $this->paymentAmount,
            'payment_date' => $this->paymentDate,
            'reference' => $this->paymentReference ?: null,
            'status' => $this->paymentStatus,
            'notes' => $this->paymentNotes ?: null,
        ];

        if ($this->editingPaymentId) {
            $paymentClass::findOrFail($this->editingPaymentId)->update($data);
            $this->dispatch('notify', message: 'Payment updated.', type: 'success');
        } else {
            $paymentClass::create($data);
            $this->dispatch('notify', message: 'Payment recorded.', type: 'success');
        }

        $this->closePaymentModal();
        unset($this->viewingPartner);
    }

    public function deletePayment(int $id): void
    {
        $paymentClass = $this->paymentClass();
        $paymentClass::findOrFail($id)->delete();
        unset($this->viewingPartner);
        $this->dispatch('notify', message: 'Payment deleted.', type: 'success');
    }

    // ── Commission Calculations ───────────────────────────────────────

    public function calculateCommission(int $partnerId): void
    {
        $partnerClass = $this->partnerClass();
        $partner = $partnerClass::findOrFail($partnerId);

        // Try to use a service if available
        if (class_exists(\App\Services\PartnerCommissionService::class)) {
            try {
                $result = app(\App\Services\PartnerCommissionService::class)->calculate($partner);
                $this->dispatch('notify', message: "Commission calculated: " . number_format($result, 0), type: 'success');
                unset($this->viewingPartner);
                return;
            } catch (\Exception $e) {
                // Fall through to manual calculation
            }
        }

        // Manual calculation fallback
        try {
            $pendingAmount = 0;

            // Check if orders table has partner_id and commission_amount columns
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'partner_id')) {
                $pendingAmount = DB::table('orders')
                    ->where('partner_id', $partnerId)
                    ->where(function ($q) {
                        $q->where('commission_paid', false)
                          ->orWhereNull('commission_paid');
                    })
                    ->sum('commission_amount');
            }

            // Also check purchase batches for this partner
            $purchaseTotal = DB::table('purchase_batches')
                ->where('partner_id', $partnerId)
                ->sum('total_cost');

            $commissionRate = $partner->commission_rate ?? 0;
            $calculatedCommission = $purchaseTotal * ($commissionRate / 100);

            // Create a new calculation record
            $calculationClass = $this->calculationClass();
            $calculationClass::create([
                'partner_id' => $partnerId,
                'total_sales' => $purchaseTotal,
                'commission_amount' => $calculatedCommission + $pendingAmount,
                'payment_amount' => 0,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'status' => 'pending',
            ]);

            $this->dispatch('notify', message: "Commission calculated: " . number_format($calculatedCommission + $pendingAmount, 0) . " (pending approval)", type: 'success');
            unset($this->viewingPartner);
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error calculating commission: ' . $e->getMessage(), type: 'error');
        }
    }

    public function approveCalculation(int $calculationId): void
    {
        $calculationClass = $this->calculationClass();
        $calculation = $calculationClass::findOrFail($calculationId);
        $calculation->update(['status' => 'approved']);
        unset($this->viewingPartner);
        $this->dispatch('notify', message: 'Calculation approved.', type: 'success');
    }

    public function markCalculationPaid(int $calculationId): void
    {
        $calculationClass = $this->calculationClass();
        $calculation = $calculationClass::findOrFail($calculationId);
        $calculation->update([
            'status' => 'paid',
            'payment_amount' => $calculation->commission_amount,
        ]);
        unset($this->viewingPartner);
        $this->dispatch('notify', message: 'Calculation marked as paid.', type: 'success');
    }

    public function deleteCalculation(int $calculationId): void
    {
        $calculationClass = $this->calculationClass();
        $calculation = $calculationClass::findOrFail($calculationId);

        if ($calculation->status === 'paid') {
            $this->dispatch('notify', message: 'Cannot delete paid calculations.', type: 'error');
            return;
        }

        $calculation->delete();
        unset($this->viewingPartner);
        $this->dispatch('notify', message: 'Calculation deleted.', type: 'success');
    }

    // ── Stats ─────────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        try {
            $partnerClass = $this->partnerClass();
            $calculationClass = $this->calculationClass();
            $paymentClass = $this->paymentClass();

            return [
                'total' => $partnerClass::count(),
                'active' => $partnerClass::where('status', 'active')->count(),
                'pending_commissions' => $calculationClass::where('status', 'pending')->sum('commission_amount'),
                'total_paid' => $paymentClass::where('status', 'completed')->sum('amount'),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'pending_commissions' => 0,
                'total_paid' => 0,
            ];
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $modelClass = $this->partnerClass();

        $partners = $modelClass::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->withCount(['payments', 'calculations', 'purchaseBatches'])
            ->withSum(['payments as total_paid' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->withSum(['calculations as pending_commission' => fn($q) => $q->where('status', 'pending')], 'commission_amount')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.finance.partner-index', [
            'partners' => $partners,
        ]);
    }
}
