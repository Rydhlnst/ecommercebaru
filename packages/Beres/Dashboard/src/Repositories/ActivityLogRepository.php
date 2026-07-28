<?php

namespace Beres\Dashboard\Repositories;

use Beres\Dashboard\Contracts\ActivityLogRepositoryInterface;
use Beres\Dashboard\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function __construct(
        protected ActivityLog $model
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
    public function getRecent(int $hours = 24, int $limit = 10): array
    {
        return $this->model
            ->recent($hours)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getByAction(string $action, int $limit = 10): array
    {
        return $this->model
            ->action($action)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getByUser(int $userId, string $userType = 'admin', int $limit = 10): array
    {
        return $this->model
            ->forUser($userId, $userType)
            ->with('subject')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getBySubject(string $subjectType, int $subjectId, int $limit = 10): array
    {
        return $this->model
            ->forSubject($subjectType)
            ->where('subject_id', $subjectId)
            ->with('user')
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
