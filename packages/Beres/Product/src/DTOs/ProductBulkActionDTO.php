<?php

namespace Beres\Product\DTOs;

class ProductBulkActionDTO
{
    public function __construct(
        public readonly array $productIds,
        public readonly string $action,
        public readonly ?array $data = null,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productIds: $data['product_ids'],
            action: $data['action'],
            data: $data['data'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'product_ids' => $this->productIds,
            'action'      => $this->action,
            'data'        => $this->data,
        ];
    }

    /**
     * Get valid actions.
     */
    public static function getValidActions(): array
    {
        return [
            'activate',
            'deactivate',
            'delete',
            'update_price',
            'update_quantity',
        ];
    }
}
