<?php

namespace Beres\Customer\DTOs;

class CustomerDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly ?string $group,
        public readonly int $ordersCount,
        public readonly float $totalSpent,
        public readonly array $addresses,
        public readonly ?string $createdAt,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            status: $data['status'] ? 'active' : 'inactive',
            group: $data['group'] ?? null,
            ordersCount: $data['orders_count'] ?? 0,
            totalSpent: (float) ($data['total_spent'] ?? 0),
            addresses: $data['addresses'] ?? [],
            createdAt: $data['created_at'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->firstName,
            'last_name'     => $this->lastName,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'status'        => $this->status,
            'group'         => $this->group,
            'orders_count'  => $this->ordersCount,
            'total_spent'   => $this->totalSpent,
            'addresses'     => $this->addresses,
            'created_at'    => $this->createdAt,
        ];
    }

    /**
     * Get full name.
     */
    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * Check if customer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
