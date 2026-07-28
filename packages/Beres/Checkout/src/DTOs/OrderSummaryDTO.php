<?php

namespace Beres\Checkout\DTOs;

class OrderSummaryDTO
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $shippingCost,
        public readonly float $taxAmount,
        public readonly float $discountAmount,
        public readonly float $grandTotal,
        public readonly string $currency,
        public readonly array $items,
        public readonly array $shippingAddress,
        public readonly string $shippingMethod,
        public readonly string $paymentMethod,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            subtotal: (float) ($data['subtotal'] ?? 0),
            shippingCost: (float) ($data['shipping_cost'] ?? 0),
            taxAmount: (float) ($data['tax_amount'] ?? 0),
            discountAmount: (float) ($data['discount_amount'] ?? 0),
            grandTotal: (float) ($data['grand_total'] ?? 0),
            currency: $data['currency'] ?? 'IDR',
            items: $data['items'] ?? [],
            shippingAddress: $data['shipping_address'] ?? [],
            shippingMethod: $data['shipping_method'] ?? '',
            paymentMethod: $data['payment_method'] ?? '',
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'subtotal'         => $this->subtotal,
            'shipping_cost'    => $this->shippingCost,
            'tax_amount'       => $this->taxAmount,
            'discount_amount'  => $this->discountAmount,
            'grand_total'      => $this->grandTotal,
            'currency'         => $this->currency,
            'items'            => $this->items,
            'shipping_address' => $this->shippingAddress,
            'shipping_method'  => $this->shippingMethod,
            'payment_method'   => $this->paymentMethod,
        ];
    }

    /**
     * Format grand total as currency.
     */
    public function formattedGrandTotal(): string
    {
        return 'Rp ' . number_format($this->grandTotal, 0, ',', '.');
    }
}
