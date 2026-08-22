<?php

namespace Webkul\Product\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Repositories\ProductRepository;

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
     *
     * @return Collection
     */
    public function getFrequentlyBoughtTogether(int $productId, int $limit = 10)
    {
        return cache()->remember(
            "frequently_bought_together_{$productId}_{$limit}",
            now()->addHours(24),
            function () use ($productId, $limit) {
                return $this->queryFrequentlyBoughtTogether($productId, $limit);
            }
        );
    }

    /**
     * Query products frequently bought together.
     *
     * @return Collection
     */
    protected function queryFrequentlyBoughtTogether(int $productId, int $limit)
    {
        $orderItems = DB::table('order_items')
            ->select('order_id')
            ->where('product_id', $productId)
            ->whereNull('parent_id')
            ->get()
            ->pluck('order_id');

        if ($orderItems->isEmpty()) {
            return collect();
        }

        $coOccurrenceProducts = DB::table('order_items')
            ->select(
                'product_id',
                DB::raw('COUNT(DISTINCT order_id) as co_occurrence_count')
            )
            ->whereIn('order_id', $orderItems)
            ->where('product_id', '!=', $productId)
            ->whereNull('parent_id')
            ->groupBy('product_id')
            ->orderByDesc('co_occurrence_count')
            ->limit($limit)
            ->pluck('product_id');

        if ($coOccurrenceProducts->isEmpty()) {
            return collect();
        }

        return $this->productRepository
            ->whereIn('id', $coOccurrenceProducts)
            ->where('status', 1)
            ->where('visible_individually', 1)
            ->get()
            ->sortBy(function ($product) use ($coOccurrenceProducts) {
                return $coOccurrenceProducts->search($product->id);
            })
            ->values();
    }

    /**
     * Clear the recommendation cache for a product.
     */
    public function clearCache(int $productId): void
    {
        $keys = cache()->getStore()->tags(['recommendations'])->keys();

        foreach ($keys as $key) {
            if (str_contains($key, "frequently_bought_together_{$productId}")) {
                cache()->forget($key);
            }
        }
    }
}
