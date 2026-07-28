<?php

namespace Beres\Inventory\Contracts;

interface StockHistoryRepositoryInterface
{
    /**
     * Create a new stock history entry.
     */
    public function create(array $data): object;

    /**
     * Get stock history for a product.
     */
    public function getByProduct(int $productId, int $limit = 50): array;

    /**
     * Get recent stock changes.
     */
    public function getRecent(int $hours = 24, int $limit = 10): array;

    /**
     * Get low stock products.
     */
    public function getLowStockProducts(int $threshold = 10): array;
}
