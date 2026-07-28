<?php

namespace Beres\Payment\Contracts;

interface WebhookLogRepositoryInterface
{
    /**
     * Create a new webhook log.
     */
    public function create(array $data): object;

    /**
     * Mark webhook as processed.
     */
    public function markProcessed(int $id): bool;

    /**
     * Mark webhook as failed.
     */
    public function markFailed(int $id, string $error): bool;

    /**
     * Check if webhook was already processed (idempotency).
     */
    public function alreadyProcessed(string $source, string $orderId): bool;

    /**
     * Get recent webhook logs.
     */
    public function getRecent(int $limit = 20): array;
}
