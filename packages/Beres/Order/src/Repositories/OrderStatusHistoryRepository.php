<?php

namespace Beres\Order\Repositories;

use Beres\Order\Contracts\OrderStatusHistoryRepositoryInterface;
use Beres\Order\Models\OrderStatusHistory;

class OrderStatusHistoryRepository implements OrderStatusHistoryRepositoryInterface
{
    public function __construct(
        protected OrderStatusHistory $model
    ) {}

    /**
     * {@inheritdoc}
     */
    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrder(int $orderId, int $limit = 50): array
    {
        return $this->model
            ->where('order_id', $orderId)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
