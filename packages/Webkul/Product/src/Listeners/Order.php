<?php

namespace Webkul\Product\Listeners;

use Webkul\Product\Helpers\RecommendationHelper;
use Webkul\Product\Jobs\UpdateCreateInventoryIndex as UpdateCreateInventoryIndexJob;

class Order
{
    /**
     * Create a new listener instance.
     */
    public function __construct(
        protected RecommendationHelper $recommendationHelper
    ) {}

    /**
     * After order is created
     *
     * @param  \Webkul\Sale\Contracts\Order  $order
     * @return void
     */
    public function afterCancelOrCreate($order)
    {
        $productIds = $order->all_items
            ->whereNull('parent_id')
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->toArray();

        UpdateCreateInventoryIndexJob::dispatch($productIds);

        foreach ($productIds as $productId) {
            $this->recommendationHelper->clearCache($productId, $order->channel_id);
        }
    }
}
