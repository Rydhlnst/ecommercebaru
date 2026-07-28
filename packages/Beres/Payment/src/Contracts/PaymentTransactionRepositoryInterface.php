<?php

namespace Beres\Payment\Contracts;

interface PaymentTransactionRepositoryInterface
{
    /**
     * Create a new payment transaction.
     */
    public function create(array $data): object;

    /**
     * Get transaction by order ID.
     */
    public function getByOrderId(int $orderId): ?object;

    /**
     * Get transaction by Midtrans order ID.
     */
    public function getByMidtransOrderId(string $midtransOrderId): ?object;

    /**
     * Update transaction status.
     */
    public function updateStatus(int $id, string $status, string $fraudStatus = null, array $response = null): bool;

    /**
     * Get recent transactions.
     */
    public function getRecent(int $limit = 20): array;
}
