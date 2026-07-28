<?php

namespace Beres\Checkout\Repositories;

use Beres\Checkout\Contracts\CheckoutSessionRepositoryInterface;
use Beres\Checkout\Models\CheckoutSession;

class CheckoutSessionRepository implements CheckoutSessionRepositoryInterface
{
    public function __construct(
        protected CheckoutSession $model
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
    public function getById(int $id): ?object
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveByCartId(int $cartId): ?object
    {
        return $this->model
            ->where('cart_id', $cartId)
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $session = $this->model->find($id);

        if (!$session) {
            return false;
        }

        return $session->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function markCompleted(int $id): bool
    {
        return $this->update($id, ['status' => 'completed']);
    }
}
