<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get orders by user
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->findAllBy('status', $status);
    }

    /**
     * Get pending orders
     */
    public function getPending(): Collection
    {
        return $this->model->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get completed orders
     */
    public function getCompleted(): Collection
    {
        return $this->model->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders within date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): Model
    {
        $updateData = ['status' => $status];

        switch ($status) {
            case 'shipped':
                $updateData['shipped_at'] = now();
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                break;
        }

        return $this->update($id, $updateData);
    }

    /**
     * Add tracking number
     */
    public function addTracking(int $id, string $trackingNumber): Model
    {
        return $this->update($id, [
            'tracking_number' => $trackingNumber,
            'status' => 'shipped',
            'shipped_at' => now()
        ]);
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->model->sum('total_amount');
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
    }

    /**
     * Get order count by status
     */
    public function getCountByStatus(): array
    {
        return $this->model->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get today's orders
     */
    public function getTodayOrders(): Collection
    {
        return $this->model->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get today's revenue
     */
    public function getTodayRevenue(): float
    {
        return $this->model->whereDate('created_at', today())
            ->sum('total_amount');
    }

    /**
     * Find by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Model
    {
        return $this->findBy('order_number', $orderNumber);
    }

    /**
     * Create order with items
     */
    public function createWithItems(array $orderData, array $items): Model
    {
        $order = $this->create($orderData);

        foreach ($items as $item) {
            $order->items()->create($item);
        }

        return $order->fresh();
    }

    /**
     * Get orders for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->model->with('items', 'user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get monthly orders
     */
    public function getMonthlyOrders(int $year, int $month): Collection
    {
        return $this->model->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
