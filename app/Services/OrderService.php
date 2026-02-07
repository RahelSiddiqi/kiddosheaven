<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct($orderRepository);
    }

    /**
     * Get orders by user
     */
    public function getByUser(int $userId): Collection
    {
        return $this->orderRepository->getByUser($userId);
    }

    /**
     * Get pending orders
     */
    public function getPending(): Collection
    {
        return $this->orderRepository->getPending();
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->orderRepository->getRecent($limit);
    }

    /**
     * Create order
     */
    public function createOrder(array $data): Order
    {
        // Generate order number if not provided
        if (!isset($data['order_number'])) {
            $data['order_number'] = $this->generateOrderNumber();
        }

        return $this->orderRepository->create($data);
    }

    /**
     * Create order with items
     */
    public function createOrderWithItems(array $orderData, array $items): Order
    {
        return $this->orderRepository->createWithItems($orderData, $items);
    }

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): Order
    {
        return $this->orderRepository->updateStatus($id, $status);
    }

    /**
     * Mark order as shipped
     */
    public function shipOrder(int $id, string $trackingNumber = null): Order
    {
        $updateData = [
            'status' => 'shipped',
            'shipped_at' => now()
        ];

        if ($trackingNumber) {
            $updateData['tracking_number'] = $trackingNumber;
        }

        return $this->orderRepository->update($id, $updateData);
    }

    /**
     * Mark order as delivered
     */
    public function deliverOrder(int $id): Order
    {
        return $this->orderRepository->update($id, [
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $id, string $reason = null): Order
    {
        $updateData = [
            'status' => 'cancelled',
            'cancelled_at' => now()
        ];

        if ($reason) {
            $updateData['cancelled_reason'] = $reason;
        }

        return $this->orderRepository->update($id, $updateData);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $id, string $status, string $paymentId = null): Order
    {
        return $this->orderRepository->updatePaymentStatus($id, $status, $paymentId);
    }

    /**
     * Find by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->orderRepository->getTotalRevenue();
    }

    /**
     * Get today's revenue
     */
    public function getTodayRevenue(): float
    {
        return $this->orderRepository->getTodayRevenue();
    }

    /**
     * Get orders by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->orderRepository->getByDateRange($startDate, $endDate);
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->orderRepository->getRevenueByDateRange($startDate, $endDate);
    }

    /**
     * Get order statistics
     */
    public function getStats(): array
    {
        return [
            'total_orders' => $this->orderRepository->count(),
            'pending_orders' => $this->orderRepository->getPending()->count(),
            'today_orders' => $this->orderRepository->getTodayOrders()->count(),
            'today_revenue' => $this->orderRepository->getTodayRevenue(),
            'total_revenue' => $this->orderRepository->getTotalRevenue(),
            'status_breakdown' => $this->orderRepository->getCountByStatus()
        ];
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'KH-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while ($this->orderRepository->findByOrderNumber($orderNumber));

        return $orderNumber;
    }

    /**
     * Get orders for export
     */
    public function getForExport(array $filters = []): Collection
    {
        return $this->orderRepository->getForExport($filters);
    }
}
