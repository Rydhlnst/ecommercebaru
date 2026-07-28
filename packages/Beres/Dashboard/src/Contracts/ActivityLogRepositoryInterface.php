<?php

namespace Beres\Dashboard\Contracts;

interface ActivityLogRepositoryInterface
{
    /**
     * Create a new activity log entry.
     */
    public function create(array $data): object;

    /**
     * Get recent activities.
     */
    public function getRecent(int $hours = 24, int $limit = 10): array;

    /**
     * Get activities by action.
     */
    public function getByAction(string $action, int $limit = 10): array;

    /**
     * Get activities by user.
     */
    public function getByUser(int $userId, string $userType = 'admin', int $limit = 10): array;

    /**
     * Get activities by subject.
     */
    public function getBySubject(string $subjectType, int $subjectId, int $limit = 10): array;

    /**
     * Delete old activities.
     */
    public function prune(int $daysToKeep = 90): int;
}
