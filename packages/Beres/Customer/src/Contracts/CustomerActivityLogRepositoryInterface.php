<?php

namespace Beres\Customer\Contracts;

interface CustomerActivityLogRepositoryInterface
{
    /**
     * Create a new activity log entry.
     */
    public function create(array $data): object;

    /**
     * Get activities for a customer.
     */
    public function getByCustomer(int $customerId, int $limit = 50): array;

    /**
     * Get recent activities.
     */
    public function getRecent(int $hours = 24, int $limit = 10): array;

    /**
     * Delete old activities.
     */
    public function prune(int $daysToKeep = 90): int;
}
