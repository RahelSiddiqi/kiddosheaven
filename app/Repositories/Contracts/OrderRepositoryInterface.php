<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * Get orders with items and user
     *
     * @return Collection
     */
    public function allWithRelations(): Collection;

    /**
     * Get orders by user
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUser(int $userId): Collection;

    /**
     * Get orders by status
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get recent orders
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentOrders(int $limit = 10): Collection;

    /**
     * Get orders by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Update order status
     *
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $orderId, string $status): bool;

    /**
     * Get pending orders count
     *
     * @return int
     */
    public function getPendingCount(): int;

    /**
     * Calculate total revenue
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalRevenue(?string $startDate = null, ?string $endDate = null): float;

    /**
     * Search orders
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;
}
