<?php

namespace Beres\Customer\Repositories;

use Beres\Customer\Contracts\CustomerNoteRepositoryInterface;
use Beres\Customer\Models\CustomerNote;

class CustomerNoteRepository implements CustomerNoteRepositoryInterface
{
    public function __construct(
        protected CustomerNote $model
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
            ->with('admin')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $note = $this->model->find($id);

        if (!$note) {
            return false;
        }

        return $note->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $note = $this->model->find($id);

        if (!$note) {
            return false;
        }

        return $note->delete();
    }
}
