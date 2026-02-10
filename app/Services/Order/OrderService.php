<?php

namespace App\Services\Order;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get all orders with relations
     *
     * @return Collection
     */
    public function getAllWithRelations(): Collection
    {
        return $this->orderRepository->allWithRelations();
    }

    /**
     * Get paginated orders
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($perPage);
    }

    /**
     * Find order by ID
     *
     * @param int $id
     * @return \App\Models\Order|null
     */
    public function findById(int $id): ?\App\Models\Order
    {
        return $this->orderRepository->find($id);
    }

    /**
     * Get orders by user
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->getByUser($userId, $perPage);
    }

    /**
     * Get orders by status
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->getByStatus($status, $perPage);
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function create(array $data): \App\Models\Order
    {
        try {
            DB::beginTransaction();

            // Generate order number if not provided
            if (!isset($data['order_number'])) {
                $data['order_number'] = $this->generateOrderNumber();
            }

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            // Calculate totals if not provided
            if (!isset($data['total_amount']) && isset($data['items'])) {
                $totals = $this->calculateOrderTotals($data['items']);
                $data['subtotal'] = $totals['subtotal'];
                $data['tax_amount'] = $totals['tax'];
                $data['total_amount'] = $totals['total'];
            }

            // Create order
            $order = $this->orderRepository->create($data);

            // Create order items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->createOrderItems($order, $data['items']);
            }

            DB::commit();

            return $order->fresh(['items', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update order
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Order
     */
    public function update(int $id, array $data): \App\Models\Order
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->update($id, $data);

            // Update items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                // Delete existing items
                $order->items()->delete();
                // Create new items
                $this->createOrderItems($order, $data['items']);
                // Recalculate totals
                $this->recalculateTotals($order);
            }

            DB::commit();

            return $order->fresh(['items', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update order status
     *
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        try {
            $result = $this->orderRepository->updateStatus($orderId, $status);

            // Handle stock adjustments based on status
            if ($result) {
                $this->handleStatusStockAdjustment($orderId, $status);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel order
     *
     * @param int $orderId
     * @return bool
     */
    public function cancel(int $orderId): bool
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->findOrFail($orderId);

            // Restore stock for cancelled order
            foreach ($order->items as $item) {
                $this->productRepository->updateStock(
                    $item->product_id,
                    $item->quantity,
                    'increment'
                );
            }

            $result = $this->orderRepository->updateStatus($orderId, 'cancelled');

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search orders
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->search($query, $perPage);
    }

    /**
     * Get recent orders
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->orderRepository->getRecentOrders($limit);
    }

    /**
     * Get pending orders count
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        return $this->orderRepository->getPendingCount();
    }

    /**
     * Get total revenue
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        return $this->orderRepository->getTotalRevenue($startDate, $endDate);
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $orderNumber = $prefix . $date . $random;

        // Ensure uniqueness
        $counter = 1;
        while ($this->orderNumberExists($orderNumber)) {
            $orderNumber = $prefix . $date . str_pad($random + $counter, 4, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $orderNumber;
    }

    /**
     * Check if order number exists
     *
     * @param string $orderNumber
     * @return bool
     */
    protected function orderNumberExists(string $orderNumber): bool
    {
        return $this->orderRepository->exists(['order_number' => $orderNumber]);
    }

    /**
     * Calculate order totals
     *
     * @param array $items
     * @return array
     */
    protected function calculateOrderTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $subtotal += $price * $quantity;
        }

        $taxRate = 0; // Configure tax rate as needed
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    /**
     * Create order items
     *
     * @param \App\Models\Order $order
     * @param array $items
     * @return void
     */
    protected function createOrderItems(\App\Models\Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);

            // Decrease stock
            $this->productRepository->updateStock(
                $item['product_id'],
                $item['quantity'],
                'decrement'
            );
        }
    }

    /**
     * Recalculate order totals
     *
     * @param \App\Models\Order $order
     * @return void
     */
    protected function recalculateTotals(\App\Models\Order $order): void
    {
        $subtotal = $order->items->sum('subtotal');
        $taxRate = 0; // Configure tax rate
        $tax = $subtotal * ($taxRate / 100);

        $this->orderRepository->update($order->id, [
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
        ]);
    }

    /**
     * Handle stock adjustment based on status change
     *
     * @param int $orderId
     * @param string $status
     * @return void
     */
    protected function handleStatusStockAdjustment(int $orderId, string $status): void
    {
        $order = $this->orderRepository->findOrFail($orderId);

        // If order is completed, ensure stock is decremented
        if ($status === 'completed' && $order->status !== 'completed') {
            foreach ($order->items as $item) {
                $this->productRepository->updateStock(
                    $item->product_id,
                    $item->quantity,
                    'decrement'
                );
            }
        }
    }
}
