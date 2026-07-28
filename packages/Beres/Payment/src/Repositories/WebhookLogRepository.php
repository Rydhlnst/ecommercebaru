<?php

namespace Beres\Payment\Repositories;

use Beres\Payment\Contracts\WebhookLogRepositoryInterface;
use Beres\Payment\Models\WebhookLog;

class WebhookLogRepository implements WebhookLogRepositoryInterface
{
    public function __construct(
        protected WebhookLog $model
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
    public function markProcessed(int $id): bool
    {
        $log = $this->model->find($id);

        if (!$log) {
            return false;
        }

        return $log->update(['processed' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function markFailed(int $id, string $error): bool
    {
        $log = $this->model->find($id);

        if (!$log) {
            return false;
        }

        return $log->update(['error' => $error]);
    }

    /**
     * {@inheritdoc}
     */
    public function alreadyProcessed(string $source, string $orderId): bool
    {
        return $this->model
            ->where('source', $source)
            ->where('processed', true)
            ->whereJsonContains('payload->order_id', $orderId)
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecent(int $limit = 20): array
    {
        return $this->model
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
