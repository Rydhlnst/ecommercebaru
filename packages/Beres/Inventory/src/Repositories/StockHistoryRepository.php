<?php

namespace Beres\Inventory\Repositories;

use Beres\Inventory\Contracts\StockHistoryRepositoryInterface;
use Beres\Inventory\Models\StockHistory;
use Carbon\Carbon;
use Webkul\Product\Models\Product;

class StockHistoryRepository implements StockHistoryRepositoryInterface
{
    public function __construct(
        protected StockHistory $model
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
    public function getByProduct(int $productId, int $limit = 50): array
    {
        return $this->model
            ->where('product_id', $productId)
            ->with('inventorySource', 'user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecent(int $hours = 24, int $limit = 10): array
    {
        return $this->model
            ->where('created_at', '>=', now()->subHours($hours))
            ->with('product', 'inventorySource', 'user')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getLowStockProducts(int $threshold = 10): array
    {
        return Product::whereHas('inventories', function ($query) use ($threshold) {
            $query->havingRaw('SUM(qty) < ?', [$threshold])
                  ->groupBy('product_id');
        })
        ->with(['inventories.inventorySource'])
        ->get()
        ->toArray();
    }
}
