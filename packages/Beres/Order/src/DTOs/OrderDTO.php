<?php

namespace Beres\Order\DTOs;

class OrderDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $incrementId,
        public readonly string $status,
        public readonly float $grandTotal,
        public readonly string $currency,
        public readonly ?string $customerName,
        public readonly ?string $customerEmail,
        public readonly ?string $shippingMethod,
        public readonly ?string $paymentMethod,
        public readonly array $items,
        public readonly ?string $createdAt,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            incrementId: $data['increment_id'] ?? null,
            status: $data['status'],
            grandTotal: (float) $data['grand_total'],
            currency: $data['currency'] ?? 'IDR',
            customerName: $data['customer_name'] ?? null,
            customerEmail: $data['customer_email'] ?? null,
            shippingMethod: $data['shipping_method'] ?? null,
            paymentMethod: $data['payment_method'] ?? null,
            items: $data['items'] ?? [],
            createdAt: $data['created_at'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'increment_id'    => $this->incrementId,
            'status'          => $this->status,
            'grand_total'     => $this->grandTotal,
            'currency'        => $this->currency,
            'customer_name'   => $this->customerName,
            'customer_email'  => $this->customerEmail,
            'shipping_method' => $this->shippingMethod,
            'payment_method'  => $this->paymentMethod,
            'items'           => $this->items,
            'created_at'      => $this->createdAt,
        ];
    }
}
