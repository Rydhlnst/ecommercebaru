<?php

namespace Beres\Customer\Contracts;

interface CustomerNoteRepositoryInterface
{
    /**
     * Create a new note.
     */
    public function create(array $data): object;

    /**
     * Get notes for a customer.
     */
    public function getByCustomer(int $customerId, int $limit = 50): array;

    /**
     * Update a note.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a note.
     */
    public function delete(int $id): bool;
}
