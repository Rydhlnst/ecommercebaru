<?php

namespace Beres\Customer\Repositories;

use Beres\Customer\Contracts\CustomerActivityLogRepositoryInterface;
use Beres\Customer\Models\CustomerActivityLog;
use Carbon\Carbon;

class CustomerActivityLogRepository implements CustomerActivityLogRepositoryInterface
{
    public function __construct(
        protected CustomerActivityLog $model
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
    public function getByCustomer(int $customerId, int $limit = 50): array
    {
        return $this->model
            ->where('customer_id', $customerId)
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
            ->with('customer', 'user')
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
