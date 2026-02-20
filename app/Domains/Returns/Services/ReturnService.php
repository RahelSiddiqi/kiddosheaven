<?php

namespace App\Domains\Returns\Services;

use App\Domains\Returns\Models\ReturnRequest;
use App\Domains\Returns\Models\ReturnItem;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    /**
     * Create a new return request for an order.
     *
     * @param  Order  $order
     * @param  array  $data  {type, reason, customer_notes, items: [{order_item_id, quantity, condition, reason}]}
     */
    public function createRequest(Order $order, array $data): ReturnRequest
    {
        return DB::transaction(function () use ($order, $data) {
            $returnRequest = ReturnRequest::create([
                'site_id'        => $order->site_id,
                'order_id'       => $order->id,
                'user_id'        => $order->user_id,
                'type'           => $data['type'] ?? 'refund',
                'reason'         => $data['reason'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'status'         => 'pending',
            ]);

            foreach ($data['items'] ?? [] as $itemData) {
                $orderItem = $order->items()->find($itemData['order_item_id']);

                if (!$orderItem) {
                    continue;
                }

                ReturnItem::create([
                    'return_request_id' => $returnRequest->id,
                    'order_item_id'     => $orderItem->id,
                    'quantity'          => min($itemData['quantity'], $orderItem->quantity),
                    'condition'         => $itemData['condition'] ?? 'good',
                    'reason'            => $itemData['reason'] ?? null,
                    'notes'             => $itemData['notes'] ?? null,
                    'refund_amount'     => $itemData['refund_amount']
                                          ?? ($orderItem->unit_price * $itemData['quantity']),
                ]);
            }

            return $returnRequest->load('items');
        });
    }

    /**
     * Approve a return request.
     */
    public function approve(ReturnRequest $request, float $refundAmount = null, string $refundMethod = 'original_payment'): void
    {
        $request->approve(
            $refundAmount ?? $request->calculateRefundAmount(),
            $refundMethod
        );
    }

    /**
     * Decline a return request.
     */
    public function decline(ReturnRequest $request, string $reason = null): void
    {
        $request->decline($reason);
    }

    /**
     * Mark an approved return as received (item arrived back).
     */
    public function markReceived(ReturnRequest $request): void
    {
        $request->markReceived();
    }

    /**
     * Process the return (refund issued / exchange sent).
     */
    public function process(ReturnRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $request->process();

            // Update order refunded_amount
            $order = $request->order;
            $order->increment('refunded_amount', $request->refund_amount ?? 0);
        });
    }
}
