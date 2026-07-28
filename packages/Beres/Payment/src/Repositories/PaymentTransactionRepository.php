<?php

namespace Beres\Payment\Repositories;

use Beres\Payment\Contracts\PaymentTransactionRepositoryInterface;
use Beres\Payment\Models\PaymentTransaction;

class PaymentTransactionRepository implements PaymentTransactionRepositoryInterface
{
    public function __construct(
        protected PaymentTransaction $model
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
    public function getByOrderId(int $orderId): ?object
    {
        return $this->model->where('order_id', $orderId)->latest()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getByMidtransOrderId(string $midtransOrderId): ?object
    {
        return $this->model->where('order_id_midtrans', $midtransOrderId)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(int $id, string $status, string $fraudStatus = null, array $response = null): bool
    {
        $transaction = $this->model->find($id);

        if (!$transaction) {
            return false;
        }

        $updateData = ['status' => $status];

        if ($fraudStatus) {
            $updateData['fraud_status'] = $fraudStatus;
        }

        if ($response) {
            $updateData['payment_response'] = $response;
        }

        return $transaction->update($updateData);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecent(int $limit = 20): array
    {
        return $this->model
            ->with('order')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
