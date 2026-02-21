<?php

namespace App\Livewire\Admin\GiftCard;

use App\Domains\GiftCard\Models\GiftCard;
use App\Domains\GiftCard\Services\GiftCardService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Gift Cards')]
class GiftCardIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public string $status = '';   // active | inactive | expired

    // ── Issue form ─────────────────────────────────────────────────
    public bool  $showForm    = false;
    public float $amount      = 500;
    public ?string $issuedEmail = null;
    public ?string $expiresAt  = null;
    public string  $note       = '';

    // ── Top-up form ────────────────────────────────────────────────
    public bool  $showTopUp   = false;
    public ?int  $topUpCardId = null;
    public float $topUpAmount = 0;

    // ── Transaction drawer ─────────────────────────────────────────
    public bool $showTx    = false;
    public ?int $txCardId  = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    // ── Issue ──────────────────────────────────────────────────────
    public function openIssue(): void
    {
        $this->reset(['amount', 'issuedEmail', 'expiresAt', 'note']);
        $this->amount   = 500;
        $this->showForm = true;
    }

    public function issueCard(GiftCardService $service): void
    {
        $this->validate([
            'amount'       => 'required|numeric|min:1',
            'issuedEmail'  => 'nullable|email',
            'expiresAt'    => 'nullable|date|after:today',
        ]);

        $service->issue($this->amount, [
            'issued_to_email' => $this->issuedEmail ?: null,
            'expires_at'      => $this->expiresAt   ?: null,
            'note'            => $this->note         ?: null,
        ]);

        $this->showForm = false;
        $this->dispatch('toast', type: 'success', message: 'Gift card issued.');
    }

    // ── Toggle active ──────────────────────────────────────────────
    public function toggle(int $id): void
    {
        $card = GiftCard::findOrFail($id);
        $card->update(['is_active' => ! $card->is_active]);
        $this->dispatch('toast', type: 'success', message: 'Gift card updated.');
    }

    // ── Top-up ─────────────────────────────────────────────────────
    public function openTopUp(int $id): void
    {
        $this->topUpCardId = $id;
        $this->topUpAmount = 0;
        $this->showTopUp   = true;
    }

    public function applyTopUp(GiftCardService $service): void
    {
        $this->validate(['topUpAmount' => 'required|numeric|min:1']);
        $card = GiftCard::findOrFail($this->topUpCardId);
        $service->topUp($card, $this->topUpAmount, 'Admin top-up');
        $this->showTopUp = false;
        $this->dispatch('toast', type: 'success', message: "Added ৳{$this->topUpAmount} to card.");
    }

    // ── Transactions ───────────────────────────────────────────────
    public function viewTransactions(int $id): void
    {
        $this->txCardId = $id;
        $this->showTx   = true;
    }

    public function render()
    {
        $query = GiftCard::with('issuedTo')
            ->when($this->search, fn ($q, $s) =>
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('issued_to_email', 'like', "%{$s}%")
            )
            ->when($this->status === 'active', fn ($q) =>
                $q->where('is_active', true)
                  ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
                  ->where('balance', '>', 0)
            )
            ->when($this->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->status === 'expired', fn ($q) =>
                $q->where('expires_at', '<', now())
            )
            ->latest();

        $txs = $this->txCardId
            ? GiftCard::find($this->txCardId)?->transactions()->latest()->limit(30)->get()
            : collect();

        return view('livewire.admin.gift-card.gift-card-index', [
            'cards' => $query->paginate(20),
            'txs'   => $txs,
        ]);
    }
}
