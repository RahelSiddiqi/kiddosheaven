<?php

namespace App\Services\Order;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Models\Setting;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;
    protected ProductRepositoryInterface $productRepository;
    protected InventoryService $inventoryService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        InventoryService $inventoryService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->inventoryService = $inventoryService;
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
     * @return Collection
     */
    public function getByUser(int $userId, int $perPage = 20): Collection
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

            // Dispatch OrderPlaced event (triggers email, etc.)
            $order = $order->fresh(['items', 'user']);
            event(new OrderPlaced($order));

            return $order;
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

            $order = $this->orderRepository->findOrFail($id);

            // If items are being replaced, FIRST restore old stock, THEN deduct new
            if (isset($data['items']) && is_array($data['items'])) {
                // ── Step 1: Restore stock from the OLD items ───────
                foreach ($order->items as $item) {
                    $batchesUsed = $this->resolveConsumedBatches($order->id, $item);
                    if (!empty($batchesUsed)) {
                        $this->inventoryService->restoreStock(
                            productId:     $item->product_id,
                            usedBatches:   $batchesUsed,
                            variantId:     $item->product_variant_id,
                            referenceType: \App\Models\Order::class,
                            referenceId:   $order->id,
                        );
                    }
                    $this->productRepository->updateStock($item->product_id, $item->quantity, 'increment');
                }

                // ── Step 2: Delete old items ──────────────────────
                $order->items()->delete();

                // ── Step 3: Update order data ─────────────────────
                $order = $this->orderRepository->update($id, $data);

                // ── Step 4: Create new items (triggers FIFO deduction) ──
                $this->createOrderItems($order, $data['items']);

                // ── Step 5: Recalculate totals ────────────────────
                $this->recalculateTotals($order);
            } else {
                $order = $this->orderRepository->update($id, $data);
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
            $order = $this->orderRepository->findOrFail($orderId);
            $oldStatus = $order->status;

            $result = $this->orderRepository->updateStatus($orderId, $status);

            // Handle stock adjustments based on status
            if ($result) {
                $this->handleStatusStockAdjustment($orderId, $status);

                // Dispatch OrderStatusChanged event (triggers status history logging)
                event(new OrderStatusChanged($order->fresh(), $oldStatus, $status));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel order — restores stock to original batches via InventoryService.
     *
     * @param int $orderId
     * @return bool
     */
    public function cancel(int $orderId): bool
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->findOrFail($orderId);

            foreach ($order->items as $item) {
                // Build the usedBatches structure from the order_item's
                // stored COGS data so InventoryService can reverse it.
                // For multi-batch sales we restore via movements.
                $batchesUsed = $this->resolveConsumedBatches($order->id, $item);

                $this->inventoryService->restoreStock(
                    productId:     $item->product_id,
                    usedBatches:   $batchesUsed,
                    variantId:     $item->product_variant_id,
                    referenceType: \App\Models\Order::class,
                    referenceId:   $order->id,
                );

                // Re-sync the denormalised counter
                $this->productRepository->updateStock(
                    $item->product_id,
                    $item->quantity,
                    'increment'
                );
            }

            $result = $this->orderRepository->updateStatus($orderId, 'cancelled');

            DB::commit();

            // Dispatch status change event
            event(new OrderStatusChanged($order->fresh(), $order->status, 'cancelled'));

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resolve which batches were consumed for an order item.
     * Looks at inventory_movements for the exact batch→quantity mapping.
     * Falls back to the single purchase_batch_id on the order_item.
     */
    protected function resolveConsumedBatches(int $orderId, \App\Models\OrderItem $item): array
    {
        // Inventory movements logged during the original sale
        $movements = \App\Models\InventoryMovement::where('product_id', $item->product_id)
            ->where('reference_type', \App\Models\Order::class)
            ->where('reference_id', $orderId)
            ->where('movement_type', \App\Models\InventoryMovement::TYPE_SALE)
            ->get();

        if ($movements->isNotEmpty()) {
            return $movements->map(fn($m) => [
                'batch_id'  => $m->batch_id,
                'quantity'  => abs($m->quantity),
                'unit_cost' => (float) $m->unit_cost,
            ])->toArray();
        }

        // Fallback: single batch link on the order_item
        if ($item->purchase_batch_id && $item->unit_cost) {
            return [[
                'batch_id'  => $item->purchase_batch_id,
                'quantity'  => $item->quantity,
                'unit_cost' => (float) $item->unit_cost,
            ]];
        }

        // No batch data at all (legacy order) — can only restore product counter
        return [];
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
        return $this->orderRepository->where('order_number', $orderNumber)->isNotEmpty();
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

        $taxRate = $this->getTaxRate();
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    /**
     * Create order items — wired to FIFO batch deduction.
     *
     * @param \App\Models\Order $order
     * @param array $items
     * @return void
     */
    protected function createOrderItems(\App\Models\Order $order, array $items): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $variantId = $item['product_variant_id'] ?? null;
            $quantity  = $item['quantity'];
            $unitPrice = $item['price'] ?? $item['unit_price'] ?? 0;

            // ── FIFO deduction ─────────────────────────────
            // This consumes from the oldest batches and returns
            // which batches were used and at what cost.
            $usedBatches = $this->inventoryService->deductStock(
                productId:     $productId,
                quantity:      $quantity,
                variantId:     $variantId,
                referenceType: \App\Models\Order::class,
                referenceId:   $order->id,
            );

            // Calculate weighted-average COGS for this line item
            $unitCost = $this->inventoryService->weightedAverageCost($usedBatches);

            // If only one batch was consumed, link it directly.
            // For multi-batch sales the link goes to the first batch (the
            // full trail lives in inventory_movements).
            $batchId = count($usedBatches) === 1
                ? $usedBatches[0]['batch_id']
                : ($usedBatches[0]['batch_id'] ?? null);

            // ── Create order_item with COGS data ───────────
            $order->items()->create([
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'quantity'           => $quantity,
                'unit_price'         => $unitPrice,
                'unit_cost'          => $unitCost,
                'purchase_batch_id'  => $batchId,
                'total_price'        => $unitPrice * $quantity,
            ]);

            // Note: Stock deduction is already handled by InventoryService->deductStock()
            // which decrements product.stock_quantity and variant.stock_quantity to keep
            // the denormalised counters in sync with the batch records
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
        $subtotal = $order->items->sum('total_price');
        $taxRate = $this->getTaxRate();
        $tax = $subtotal * ($taxRate / 100);

        $this->orderRepository->update($order->id, [
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
        ]);
    }

    /**
     * Handle stock adjustment based on status change.
     *
     * Stock is already deducted at order creation via FIFO.
     * This method handles special transitions (e.g. cancellation via status).
     *
     * @param int $orderId
     * @param string $status
     * @return void
     */
    protected function handleStatusStockAdjustment(int $orderId, string $status): void
    {
        // Stock is deducted at creation time. If we transition to 'cancelled'
        // through updateStatus() instead of cancel(), handle the restoration.
        if ($status === 'cancelled') {
            $order = $this->orderRepository->findOrFail($orderId);

            foreach ($order->items as $item) {
                $batchesUsed = $this->resolveConsumedBatches($order->id, $item);

                if (!empty($batchesUsed)) {
                    $this->inventoryService->restoreStock(
                        productId:     $item->product_id,
                        usedBatches:   $batchesUsed,
                        variantId:     $item->product_variant_id,
                        referenceType: \App\Models\Order::class,
                        referenceId:   $order->id,
                    );
                }

                $this->productRepository->updateStock(
                    $item->product_id,
                    $item->quantity,
                    'increment'
                );
            }
        }
    }

    /**
     * Get the configured tax rate from settings, defaulting to 0.
     */
    protected function getTaxRate(): float
    {
        try {
            $setting = Setting::where('key', 'tax_rate')->first();
            return $setting ? (float) $setting->value : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
