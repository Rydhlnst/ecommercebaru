<?php

namespace Beres\Inventory\DTOs;

class InventoryStatsDTO
{
    public function __construct(
        public readonly int $totalProducts,
        public readonly int $totalStock,
        public readonly int $lowStockProducts,
        public readonly int $outOfStockProducts,
        public readonly float $totalValue,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            totalProducts: $data['total_products'] ?? 0,
            totalStock: $data['total_stock'] ?? 0,
            lowStockProducts: $data['low_stock_products'] ?? 0,
            outOfStockProducts: $data['out_of_stock_products'] ?? 0,
            totalValue: (float) ($data['total_value'] ?? 0),
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'total_products'       => $this->totalProducts,
            'total_stock'          => $this->totalStock,
            'low_stock_products'   => $this->lowStockProducts,
            'out_of_stock_products' => $this->outOfStockProducts,
            'total_value'          => $this->totalValue,
        ];
    }
}
