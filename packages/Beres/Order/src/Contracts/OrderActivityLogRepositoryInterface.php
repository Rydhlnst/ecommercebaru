<?php

namespace Beres\Order\Contracts;

interface OrderActivityLogRepositoryInterface
{
    /**
     * Create a new activity log entry.
     */
    public function create(array $data): object;

    /**
     * Get activities for an order.
     */
    public function getByOrder(int $orderId, int $limit = 50): array;

    /**
     * Get recent activities.
     */
    public function getRecent(int $hours = 24, int $limit = 10): array;
}
