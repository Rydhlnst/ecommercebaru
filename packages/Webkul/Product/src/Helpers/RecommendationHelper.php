<?php

namespace Webkul\Product\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Sales\Models\Order;

class RecommendationHelper
{
    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository
    ) {}

    /**
     * Get frequently bought together products for a given product.
     *
     * This analyzes order history to find products that are frequently
     * purchased together with the specified product (co-occurrence analysis).
     */
    public function getFrequentlyBoughtTogether(int $productId, int $limit = 10): Collection
    {
        $channelId = core()->getCurrentChannel()->id;

        $productIds = collect(cache()->remember(
            $this->cacheKey($productId, $channelId, $limit),
            now()->addHours(24),
            function () use ($productId, $channelId, $limit) {
                return $this->queryFrequentlyBoughtTogether($productId, $channelId, $limit);
            }
        ));

        if ($productIds->isEmpty()) {
            return collect();
        }

        return $this->getAvailableProducts($productIds, $channelId);
    }

    /**
     * Query products frequently bought together.
     */
    protected function queryFrequentlyBoughtTogether(int $productId, int $channelId, int $limit): Collection
    {
        return DB::table('order_items as source_items')
            ->join('orders', 'orders.id', '=', 'source_items.order_id')
            ->join('order_items as candidate_items', 'candidate_items.order_id', '=', 'source_items.order_id')
            ->where('orders.channel_id', $channelId)
            ->whereNotIn('orders.status', [Order::STATUS_CANCELED, Order::STATUS_CLOSED])
            ->where('source_items.product_id', $productId)
            ->whereNull('source_items.parent_id')
            ->whereColumn('source_items.qty_canceled', '<', 'source_items.qty_ordered')
            ->where('candidate_items.product_id', '!=', $productId)
            ->whereNull('candidate_items.parent_id')
            ->whereColumn('candidate_items.qty_canceled', '<', 'candidate_items.qty_ordered')
            ->select(
                'candidate_items.product_id',
                DB::raw('COUNT(DISTINCT candidate_items.order_id) as co_occurrence_count')
            )
            ->groupBy('candidate_items.product_id')
            ->orderByDesc('co_occurrence_count')
            ->limit($limit)
            ->pluck('product_id');
    }

    /**
     * Fetch products that are currently available in the active channel.
     */
    protected function getAvailableProducts(Collection $productIds, int $channelId): Collection
    {
        $attributeIds = DB::table('attributes')
            ->whereIn('code', ['status', 'visible_individually'])
            ->pluck('id', 'code');

        if (! $attributeIds->has('status') || ! $attributeIds->has('visible_individually')) {
            return collect();
        }

        return $this->productRepository
            ->whereIn('id', $productIds)
            ->whereHas('channels', fn ($query) => $query->where('channels.id', $channelId))
            ->whereHas('attribute_values', fn ($query) => $query
                ->where('attribute_id', $attributeIds['status'])
                ->where('boolean_value', 1))
            ->whereHas('attribute_values', fn ($query) => $query
                ->where('attribute_id', $attributeIds['visible_individually'])
                ->where('boolean_value', 1))
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return $productIds->search($product->id);
            })
            ->values();
    }

    /**
     * Clear the recommendation cache for a product.
     */
    public function clearCache(int $productId, ?int $channelId = null, ?int $limit = null): void
    {
        $channelId ??= core()->getCurrentChannel()->id;
        $limit ??= core()->getConfigData('catalog.products.product_view_page.no_of_frequently_bought_together_products') ?: 10;

        cache()->forget($this->cacheKey($productId, $channelId, $limit));
    }

    /**
     * Build the channel-aware cache key for a recommendation result.
     */
    protected function cacheKey(int $productId, int $channelId, int $limit): string
    {
        return "frequently_bought_together_{$channelId}_{$productId}_{$limit}";
    }
}
