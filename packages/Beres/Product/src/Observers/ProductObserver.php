<?php

namespace Beres\Product\Observers;

use Beres\Product\Models\ProductActivityLog;
use Webkul\Product\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ProductActivityLog::log('created', $product, null, $product->toArray());
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $oldValues = $product->getChanges();
        $changedAttributes = array_keys($oldValues);

        // Remove updated_at from changes
        unset($oldValues['updated_at']);

        if (!empty($oldValues)) {
            ProductActivityLog::log('updated', $product, $oldValues, $product->toArray());
        }

        // Check for status change
        if (in_array('status', $changedAttributes)) {
            $oldStatus = $product->getOriginal('status');
            $newStatus = $product->status;

            ProductActivityLog::log(
                $newStatus ? 'activated' : 'deactivated',
                $product,
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ProductActivityLog::log('deleted', $product, $product->toArray());
    }
}
