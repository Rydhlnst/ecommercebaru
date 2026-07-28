<?php

namespace Beres\Order\Contracts;

interface OrderStatusHistoryRepositoryInterface
{
    /**
     * Create a new status history entry.
     */
    public function create(array $data): object;

    /**
     * Get status history for an order.
     */
    public function getByOrder(int $orderId, int $limit = 50): array;
}
