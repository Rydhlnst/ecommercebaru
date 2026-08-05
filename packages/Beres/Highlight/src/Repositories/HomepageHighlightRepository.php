<?php

namespace Beres\Highlight\Repositories;

use Beres\Highlight\Models\HomepageHighlight;
use Illuminate\Support\Collection;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;

class HomepageHighlightRepository
{
    public function __construct(
        protected HomepageHighlight $model
    ) {}

    /**
     * Get all active highlights for a section, ordered by sort_order.
     */
    public function getActiveBySection(string $section): Collection
    {
        return $this->model
            ->where('section', $section)
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get all highlights for a section (including inactive), with relations eager-loaded.
     */
    public function getBySection(string $section): Collection
    {
        $highlights = $this->model
            ->where('section', $section)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $productIds = $highlights->where('entity_type', HomepageHighlight::TYPE_PRODUCT)->pluck('entity_id');

        $categoryIds = $highlights->where('entity_type', HomepageHighlight::TYPE_CATEGORY)->pluck('entity_id');

        $products = $productIds->isNotEmpty()
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        $categories = $categoryIds->isNotEmpty()
            ? Category::whereIn('id', $categoryIds)->get()->keyBy('id')
            : collect();

        return $highlights->map(function ($highlight) use ($products, $categories) {
            if ($highlight->entity_type === HomepageHighlight::TYPE_PRODUCT) {
                $highlight->setRelation('entity', $products->get($highlight->entity_id));
            } else {
                $highlight->setRelation('entity', $categories->get($highlight->entity_id));
            }

            return $highlight;
        });
    }

    /**
     * Check if a highlight already exists.
     */
    public function exists(string $section, string $entityType, int $entityId): bool
    {
        return $this->model
            ->where('section', $section)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->exists();
    }

    /**
     * Count active highlights in a section.
     */
    public function countActive(string $section): int
    {
        return $this->model
            ->where('section', $section)
            ->where('status', true)
            ->count();
    }

    /**
     * Add a highlight to a section.
     */
    public function add(string $section, string $entityType, int $entityId): ?HomepageHighlight
    {
        if ($this->exists($section, $entityType, $entityId)) {
            return null;
        }

        $nextSort = $this->model->where('section', $section)->max('sort_order') ?? -1;

        return $this->model->create([
            'section' => $section,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'sort_order' => $nextSort + 1,
            'status' => true,
        ]);
    }

    /**
     * Reorder highlights within a section by an array of IDs.
     */
    public function reorder(string $section, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $this->model
                ->where('id', $id)
                ->where('section', $section)
                ->update(['sort_order' => $index]);
        }
    }
}
