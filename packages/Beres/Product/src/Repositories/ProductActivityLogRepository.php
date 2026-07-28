<?php

namespace Beres\Product\Repositories;

use Beres\Product\Contracts\ProductActivityLogRepositoryInterface;
use Beres\Product\Models\ProductActivityLog;
use Carbon\Carbon;

class ProductActivityLogRepository implements ProductActivityLogRepositoryInterface
{
    public function __construct(
        protected ProductActivityLog $model
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
    public function getByProduct(int $productId, int $limit = 50): array
    {
        return $this->model
            ->where('product_id', $productId)
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
            ->recent($hours)
            ->with('product', 'user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function prune(int $daysToKeep = 90): int
    {
        return $this->model
            ->where('created_at', '<', Carbon::now()->subDays($daysToKeep))
            ->delete();
    }
}
