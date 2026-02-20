<?php

namespace App\Domains\Payment\Services;

use App\Domains\Payment\Models\PaymentGateway;
use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Get active gateways for the current site.
     */
    public function activeGateways(): \Illuminate\Database\Eloquent\Collection
    {
        return PaymentGateway::active()->get();
    }

    /**
     * Find gateway by code, respecting site scope.
     */
    public function gateway(string $code): ?PaymentGateway
    {
        return PaymentGateway::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Create a pending transaction record before redirecting to gateway.
     */
    public function createPendingTransaction(Order $order, string $gatewayCode, float $amount): PaymentTransaction
    {
        return PaymentTransaction::create([
            'site_id'      => $order->site_id,
            'order_id'     => $order->id,
            'gateway_code' => $gatewayCode,
            'type'         => PaymentTransaction::TYPE_CHARGE,
            'status'       => PaymentTransaction::STATUS_PENDING,
            'amount'       => $amount,
            'currency'     => config('app.currency', 'BDT'),
        ]);
    }

    /**
     * Mark a transaction as successful and update the order.
     */
    public function markPaid(PaymentTransaction $tx, array $gatewayData = []): PaymentTransaction
    {
        $tx->update([
            'status'          => PaymentTransaction::STATUS_SUCCESS,
            'transaction_id'  => $gatewayData['transaction_id'] ?? null,
            'gateway_reference' => $gatewayData['reference'] ?? null,
            'fee_amount'      => $gatewayData['fee'] ?? 0,
            'net_amount'      => $tx->amount - ($gatewayData['fee'] ?? 0),
            'raw_response'    => $gatewayData,
            'paid_at'         => now(),
        ]);

        // Update order payment status
        $tx->order()->update([
            'payment_status'   => 'paid',
            'payment_method'   => $tx->gateway_code,
        ]);

        return $tx->fresh();
    }

    /**
     * Mark a transaction as failed.
     */
    public function markFailed(PaymentTransaction $tx, string $error = '', array $raw = []): PaymentTransaction
    {
        $tx->update([
            'status'        => PaymentTransaction::STATUS_FAILED,
            'error_message' => $error,
            'raw_response'  => $raw,
        ]);

        return $tx->fresh();
    }

    /**
     * Issue a full or partial refund.
     */
    public function refund(PaymentTransaction $original, float $amount, string $reason = ''): PaymentTransaction
    {
        $refund = PaymentTransaction::create([
            'site_id'           => $original->site_id,
            'order_id'          => $original->order_id,
            'gateway_code'      => $original->gateway_code,
            'type'              => $amount >= $original->amount
                ? PaymentTransaction::TYPE_REFUND
                : PaymentTransaction::TYPE_PARTIAL_REFUND,
            'status'            => PaymentTransaction::STATUS_REFUNDED,
            'amount'            => $amount,
            'currency'          => $original->currency,
            'gateway_reference' => $original->gateway_reference,
            'raw_response'      => ['reason' => $reason],
            'refunded_at'       => now(),
        ]);

        if ($amount >= $original->amount) {
            $original->update(['status' => PaymentTransaction::STATUS_REFUNDED]);
            $original->order()->update(['payment_status' => 'refunded']);
        }

        return $refund;
    }

    /**
     * Get total paid amount for an order (net of refunds).
     */
    public function paidAmount(int $orderId): float
    {
        $charged = PaymentTransaction::where('order_id', $orderId)
            ->successful()
            ->sum('net_amount');

        $refunded = PaymentTransaction::where('order_id', $orderId)
            ->refunds()
            ->where('status', PaymentTransaction::STATUS_REFUNDED)
            ->sum('amount');

        return max(0, $charged - $refunded);
    }
}
