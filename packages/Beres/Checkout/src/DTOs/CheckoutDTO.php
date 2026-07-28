<?php

namespace Beres\Checkout\DTOs;

class CheckoutDTO
{
    public function __construct(
        public readonly int $cartId,
        public readonly ?int $customerId,
        public readonly array $shippingAddress,
        public readonly ?array $billingAddress,
        public readonly string $shippingMethod,
        public readonly float $shippingCost,
        public readonly string $paymentMethod,
        public readonly ?string $notes,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            cartId: $data['cart_id'],
            customerId: $data['customer_id'] ?? null,
            shippingAddress: $data['shipping_address'] ?? [],
            billingAddress: $data['billing_address'] ?? null,
            shippingMethod: $data['shipping_method'],
            shippingCost: (float) ($data['shipping_cost'] ?? 0),
            paymentMethod: $data['payment_method'],
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'cart_id'           => $this->cartId,
            'customer_id'       => $this->customerId,
            'shipping_address'  => $this->shippingAddress,
            'billing_address'   => $this->billingAddress,
            'shipping_method'   => $this->shippingMethod,
            'shipping_cost'     => $this->shippingCost,
            'payment_method'    => $this->paymentMethod,
            'notes'             => $this->notes,
        ];
    }
}
