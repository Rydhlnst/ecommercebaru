<?php

namespace Beres\Product\Services;

use Beres\Product\Contracts\ProductActivityLogRepositoryInterface;
use Beres\Product\DTOs\ProductDTO;
use Beres\Product\DTOs\ProductBulkActionDTO;
use Beres\Product\Models\ProductActivityLog;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        protected ProductActivityLogRepositoryInterface $activityLogRepository
    ) {}

    /**
     * Get product as DTO.
     */
    public function getProductDto(Product $product): ProductDTO
    {
        return ProductDTO::fromArray([
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->url_key,
            'sku'               => $product->sku,
            'price'             => $product->price,
            'special_price'     => $product->special_price,
            'quantity'          => $product->totalQuantity(),
            'status'            => $product->status ? 'active' : 'inactive',
            'visibility'        => $product->visible_individually ? 'catalog_search' : 'catalog',
            'description'       => $product->description,
            'meta_title'        => $product->meta_title,
            'meta_description'  => $product->meta_description,
            'categories'        => $product->categories->pluck('id')->toArray(),
            'images'            => $product->images->toArray(),
            'attribute_values'  => $product->attribute_values->toArray(),
        ]);
    }

    /**
     * Bulk action on products.
     */
    public function bulkAction(ProductBulkActionDTO $dto): array
    {
        $results = [
            'success' => 0,
            'failed'  => 0,
            'errors'  => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($dto->productIds as $productId) {
                try {
                    $product = Product::find($productId);

                    if (!$product) {
                        $results['failed']++;
                        $results['errors'][] = "Product {$productId} not found";
                        continue;
                    }

                    match ($dto->action) {
                        'activate'      => $this->activateProduct($product),
                        'deactivate'    => $this->deactivateProduct($product),
                        'delete'        => $this->deleteProduct($product),
                        'update_price'  => $this->updateProductPrice($product, $dto->data['price'] ?? 0),
                        'update_quantity' => $this->updateProductQuantity($product, $dto->data['quantity'] ?? 0),
                        default         => throw new \Exception("Invalid action: {$dto->action}"),
                    };

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Product {$productId}: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Activate a product.
     */
    protected function activateProduct(Product $product): void
    {
        $oldValues = ['status' => $product->status];
        $product->update(['status' => 1]);
        ProductActivityLog::log('activated', $product, $oldValues, ['status' => 1]);
    }

    /**
     * Deactivate a product.
     */
    protected function deactivateProduct(Product $product): void
    {
        $oldValues = ['status' => $product->status];
        $product->update(['status' => 0]);
        ProductActivityLog::log('deactivated', $product, $oldValues, ['status' => 0]);
    }

    /**
     * Delete a product.
     */
    protected function deleteProduct(Product $product): void
    {
        $oldValues = $product->toArray();
        $product->delete();
        ProductActivityLog::log('deleted', $product, $oldValues);
    }

    /**
     * Update product price.
     */
    protected function updateProductPrice(Product $product, float $price): void
    {
        $oldValues = ['price' => $product->price];
        $product->update(['price' => $price]);
        ProductActivityLog::log('price_updated', $product, $oldValues, ['price' => $price]);
    }

    /**
     * Update product quantity.
     */
    protected function updateProductQuantity(Product $product, int $quantity): void
    {
        $oldValues = ['quantity' => $product->totalQuantity()];
        
        // Update inventory source quantity
        foreach ($product->inventories as $inventory) {
            $inventory->update(['qty' => $quantity]);
        }

        ProductActivityLog::log('quantity_updated', $product, $oldValues, ['quantity' => $quantity]);
    }

    /**
     * Get product activity log.
     */
    public function getActivityLog(int $productId, int $limit = 50): array
    {
        return $this->activityLogRepository->getByProduct($productId, $limit);
    }

    /**
     * Get recent product activities.
     */
    public function getRecentActivities(int $hours = 24, int $limit = 10): array
    {
        return $this->activityLogRepository->getRecent($hours, $limit);
    }

    /**
     * Search products.
     */
    public function search(array $filters): array
    {
        $query = Product::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        if (!empty($filters['sku'])) {
            $query->where('sku', 'LIKE', "%{$filters['sku']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
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
