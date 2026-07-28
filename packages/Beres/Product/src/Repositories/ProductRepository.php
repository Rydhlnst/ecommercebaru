<?php

namespace Beres\Product\Repositories;

use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;
use Beres\Product\Contracts\ProductActivityLogRepositoryInterface;
use Beres\Product\Models\ProductActivityLog;

class ProductRepository extends BaseProductRepository
{
    public function __construct(
        protected ProductActivityLogRepositoryInterface $activityLogRepository
    ) {
        parent::__construct();
    }

    /**
     * Create a product with activity logging.
     */
    public function createWithLog(array $data): object
    {
        $product = $this->create($data);

        ProductActivityLog::log('created', $product, null, $data);

        return $product;
    }

    /**
     * Update a product with activity logging.
     */
    public function updateWithLog(array $data, $id): bool
    {
        $product = $this->find($id);
        $oldValues = $product->toArray();

        $result = $this->update($data, $id);

        if ($result) {
            ProductActivityLog::log('updated', $product, $oldValues, $data);
        }

        return $result;
    }

    /**
     * Delete a product with activity logging.
     */
    public function deleteWithLog($id): bool
    {
        $product = $this->find($id);
        $oldValues = $product->toArray();

        $result = $this->delete($id);

        if ($result) {
            ProductActivityLog::log('deleted', $product, $oldValues);
        }

        return $result;
    }
}
