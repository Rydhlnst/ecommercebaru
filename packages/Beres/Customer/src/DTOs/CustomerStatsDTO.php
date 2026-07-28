<?php

namespace Beres\Customer\DTOs;

class CustomerStatsDTO
{
    public function __construct(
        public readonly int $customerId,
        public readonly int $totalOrders,
        public readonly float $totalSpent,
        public readonly float $averageOrderValue,
        public readonly ?string $lastOrderDate,
        public readonly int $wishlistCount,
        public readonly int $reviewCount,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            totalOrders: $data['total_orders'] ?? 0,
            totalSpent: (float) ($data['total_spent'] ?? 0),
            averageOrderValue: (float) ($data['average_order_value'] ?? 0),
            lastOrderDate: $data['last_order_date'] ?? null,
            wishlistCount: $data['wishlist_count'] ?? 0,
            reviewCount: $data['review_count'] ?? 0,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'customer_id'         => $this->customerId,
            'total_orders'        => $this->totalOrders,
            'total_spent'         => $this->totalSpent,
            'average_order_value' => $this->averageOrderValue,
            'last_order_date'     => $this->lastOrderDate,
            'wishlist_count'      => $this->wishlistCount,
            'review_count'        => $this->reviewCount,
        ];
    }
}
