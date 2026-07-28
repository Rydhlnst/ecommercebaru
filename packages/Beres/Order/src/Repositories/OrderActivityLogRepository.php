<?php

namespace Beres\Order\Repositories;

use Beres\Order\Contracts\OrderActivityLogRepositoryInterface;
use Beres\Order\Models\OrderActivityLog;
use Carbon\Carbon;

class OrderActivityLogRepository implements OrderActivityLogRepositoryInterface
{
    public function __construct(
        protected OrderActivityLog $model
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

    /**
     * {@inheritdoc}
     */
    public function getRecent(int $hours = 24, int $limit = 10): array
    {
        return $this->model
            ->where('created_at', '>=', now()->subHours($hours))
            ->with('order', 'user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
