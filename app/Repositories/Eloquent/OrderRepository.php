<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * OrderRepository constructor
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get orders with items and user
     *
     * @return Collection
     */
    public function allWithRelations(): Collection
    {
        return $this->model->with(['items.product', 'user'])->get();
    }

    /**
     * Get orders by user
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['items.product'])
            ->latest()
            ->get();
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
        return $this->model
            ->where('status', $status)
            ->with(['items.product', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent orders
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->model
            ->with(['items.product', 'user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product', 'user'])
            ->get();
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
        $order = $this->findOrFail($orderId);
        return $order->update(['status' => $status]);
    }

    /**
     * Get pending orders count
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        return $this->model->where('status', 'pending')->count();
    }

    /**
     * Calculate total revenue
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->model->whereIn('status', ['processing', 'shipped', 'delivered']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->sum('total_amount') ?? 0;
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
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                    ->orWhere('customer_name', 'like', "%{$query}%")
                    ->orWhere('customer_email', 'like', "%{$query}%")
                    ->orWhere('customer_phone', 'like', "%{$query}%");
            })
            ->with(['items.product', 'user'])
            ->latest()
            ->paginate($perPage);
    }
}
