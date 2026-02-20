<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Domains\Order\Models\Order;
use App\Domains\Returns\Models\ReturnItem;
use App\Domains\Returns\Services\ReturnService;
use Illuminate\Support\Facades\Auth;

class CreateReturnRequest extends Component
{
    public Order $order;

    #[Validate('required|in:refund,exchange,store_credit')]
    public string $type = 'refund';

    #[Validate('required|string|max:500')]
    public string $reason = '';

    #[Validate('nullable|string|max:1000')]
    public string $customer_notes = '';

    // Per-item selections: [order_item_id => ['selected' => bool, 'quantity' => int, 'condition' => string, 'reason' => string]]
    public array $items = [];

    public function mount(int $orderId): void
    {
        $this->order = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->findOrFail($orderId);

        // Only delivered orders can be returned
        abort_unless($this->order->fulfillment_status === 'delivered', 403);

        // Block if a pending/approved return already exists for this order
        abort_if(
            $this->order->returnRequests()
                ->whereNotIn('status', ['declined', 'closed'])
                ->exists(),
            422,
            'A return request already exists for this order.'
        );

        // Initialise item state
        foreach ($this->order->items as $item) {
            $this->items[$item->id] = [
                'selected'  => false,
                'quantity'  => $item->quantity,
                'condition' => 'good',
                'reason'    => 'changed_mind',
            ];
        }
    }

    public function submit(ReturnService $service): mixed
    {
        $this->validate();

        $selectedItems = collect($this->items)
            ->filter(fn($i) => $i['selected'])
            ->map(fn($data, $itemId) => [
                'order_item_id' => (int) $itemId,
                'quantity'      => max(1, (int) $data['quantity']),
                'condition'     => $data['condition'],
                'reason'        => $data['reason'],
            ])
            ->values()
            ->all();

        if (empty($selectedItems)) {
            $this->addError('items', 'Please select at least one item to return.');
            return null;
        }

        $returnRequest = $service->createRequest($this->order, [
            'type'           => $this->type,
            'reason'         => $this->reason,
            'customer_notes' => $this->customer_notes,
            'items'          => $selectedItems,
        ]);

        session()->flash('success', 'Return request #' . $returnRequest->return_number . ' submitted. We\'ll review it within 1-2 business days.');

        return redirect()->route('customer.orders.show', $this->order->id);
    }

    public function render()
    {
        return view('livewire.storefront.create-return-request');
    }
}
