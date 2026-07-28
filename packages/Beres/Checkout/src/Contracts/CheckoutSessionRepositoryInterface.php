<?php

namespace Beres\Checkout\Contracts;

interface CheckoutSessionRepositoryInterface
{
    /**
     * Create a new checkout session.
     */
    public function create(array $data): object;

    /**
     * Get session by ID.
     */
    public function getById(int $id): ?object;

    /**
     * Get active session for cart.
     */
    public function getActiveByCartId(int $cartId): ?object;

    /**
     * Update session.
     */
    public function update(int $id, array $data): bool;

    /**
     * Mark session as completed.
     */
    public function markCompleted(int $id): bool;
}
