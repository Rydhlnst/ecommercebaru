<?php

namespace Beres\Inventory\Services;

use Beres\Inventory\Contracts\StockHistoryRepositoryInterface;
use Beres\Inventory\DTOs\InventoryStatsDTO;
use Beres\Inventory\DTOs\StockHistoryDTO;
use Beres\Inventory\Models\StockHistory;
use Webkul\Product\Models\Product;
use Webkul\Inventory\Models\InventorySource;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Low stock threshold.
     */
    const LOW_STOCK_THRESHOLD = 10;

    public function __construct(
        protected StockHistoryRepositoryInterface $stockHistoryRepository
    ) {}

    /**
     * Get inventory statistics.
     */
    public function getStats(): InventoryStatsDTO
    {
        $products = Product::with(['inventories'])->get();

        $totalStock = 0;
        $lowStockProducts = 0;
        $outOfStockProducts = 0;
        $totalValue = 0;

        foreach ($products as $product) {
            $stock = $product->totalQuantity();
            $totalStock += $stock;

            if ($stock == 0) {
                $outOfStockProducts++;
            } elseif ($stock < self::LOW_STOCK_THRESHOLD) {
                $lowStockProducts++;
            }

            $totalValue += $stock * $product->price;
        }

        return InventoryStatsDTO::fromArray([
            'total_products'       => $products->count(),
            'total_stock'          => $totalStock,
            'low_stock_products'   => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'total_value'          => $totalValue,
        ]);
    }

    /**
     * Adjust stock for a product.
     */
    public function adjustStock(
        int $productId,
        int $quantity,
        string $action,
        int $inventorySourceId = null,
        string $note = null
    ): bool {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        DB::beginTransaction();

        try {
            $oldQuantity = $product->totalQuantity();

            // Update inventory
            $inventory = $product->inventories()->firstOrCreate(
                ['inventory_source_id' => $inventorySourceId ?? 1],
                ['qty' => 0]
            );

            match ($action) {
                'add'       => $inventory->update(['qty' => $inventory->qty + $quantity]),
                'subtract'  => $inventory->update(['qty' => max(0, $inventory->qty - $quantity)]),
                'set'       => $inventory->update(['qty' => $quantity]),
                default     => throw new \Exception("Invalid action: {$action}"),
            };

            $newQuantity = $product->fresh()->totalQuantity();

            // Log stock history
            StockHistory::log(
                $productId,
                $action,
                $quantity,
                $oldQuantity,
                $newQuantity,
                $inventorySourceId,
                null,
                null,
                $note
            );

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get stock history for a product.
     */
    public function getStockHistory(int $productId, int $limit = 50): array
    {
        return $this->stockHistoryRepository->getByProduct($productId, $limit);
    }

    /**
     * Get recent stock changes.
     */
    public function getRecentChanges(int $hours = 24, int $limit = 10): array
    {
        return $this->stockHistoryRepository->getRecent($hours, $limit);
    }

    /**
     * Get low stock products.
     */
    public function getLowStockProducts(int $threshold = null): array
    {
        $threshold = $threshold ?? self::LOW_STOCK_THRESHOLD;
        return $this->stockHistoryRepository->getLowStockProducts($threshold);
    }

    /**
     * Get out of stock products.
     */
    public function getOutOfStockProducts(): array
    {
        return Product::whereHas('inventories', function ($query) {
            $query->havingRaw('SUM(qty) = 0')
                  ->groupBy('product_id');
        })
        ->get()
        ->toArray();
    }

    /**
     * Search inventory.
     */
    public function search(array $filters): array
    {
        $query = Product::with(['inventories.inventorySource']);

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        if (!empty($filters['sku'])) {
            $query->where('sku', 'LIKE', "%{$filters['sku']}%");
        }

        if (isset($filters['low_stock'])) {
            $query->whereHas('inventories', function ($q) {
                $q->havingRaw('SUM(qty) < ?', [self::LOW_STOCK_THRESHOLD])
                  ->groupBy('product_id');
            });
        }

        if (isset($filters['out_of_stock'])) {
            $query->whereHas('inventories', function ($q) {
                $q->havingRaw('SUM(qty) = 0')
                  ->groupBy('product_id');
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page'])->toArray();
        }

        return $query->get()->toArray();
    }
}
