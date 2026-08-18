<?php

namespace Beres\Highlight\Services;

use App\Models\AdminCategory;
use App\Models\AdminProduct;
use Beres\Highlight\Models\HomepageHighlight;
use Beres\Highlight\Repositories\HomepageHighlightRepository;
use Illuminate\Support\Collection;

class HomepageHighlightService
{
    public function __construct(
        protected HomepageHighlightRepository $repository,
    ) {}

    /**
     * Resolve products for a section.
     */
    public function getProducts(string $section, ?int $limit = null): Collection
    {
        $highlights = $this->repository->getActiveBySection($section);

        if ($highlights->isNotEmpty()) {
            $ids = $highlights->pluck('entity_id')
                ->when($limit !== null, fn ($ids) => $ids->take($limit))
                ->values()
                ->all();

            $products = AdminProduct::with('category', 'images', 'variations')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');

            $resolved = collect($ids)
                ->map(fn ($id) => $products->get($id))
                ->filter()
                ->values();

            if ($resolved->isNotEmpty()) {
                return $resolved;
            }
        }

        return $this->fallbackProducts($section, $limit);
    }

    /**
     * Resolve the single featured product.
     */
    public function getFeaturedProduct(): ?object
    {
        return $this->getProducts(HomepageHighlight::SECTION_FEATURED, 1)->first();
    }

    /**
     * Resolve categories for the Shop-by-Category section.
     */
    public function getCategories(int $limit = 8): Collection
    {
        $highlights = $this->repository->getActiveBySection(HomepageHighlight::SECTION_CATEGORIES);

        if ($highlights->isNotEmpty()) {
            $ids = $highlights->pluck('entity_id')->take($limit)->values()->all();

            $categories = AdminCategory::whereIn('id', $ids)->get()->keyBy('id');

            return collect($ids)
                ->map(function ($id) use ($categories) {
                    $cat = $categories->get($id);

                    return $cat ? [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'image' => $cat->image_url,
                    ] : null;
                })
                ->filter()
                ->values();
        }

        return $this->fallbackCategories($limit);
    }

    /**
     * Fallback: return products from admin_products based on section type.
     */
    protected function fallbackProducts(string $section, ?int $limit): Collection
    {
        $query = AdminProduct::with('category', 'images', 'variations')
            ->where('status', 'active');

        switch ($section) {
            case HomepageHighlight::SECTION_FEATURED:
                return $query
                    ->where('is_featured', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(1)
                    ->get();

            case HomepageHighlight::SECTION_NEW_ARRIVALS:
                return $query
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();

            case HomepageHighlight::SECTION_KITS_BUNDLES:
            case HomepageHighlight::SECTION_SEEDS:
                return $query
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();

            case HomepageHighlight::SECTION_BEST_SELLERS:
                $query = $query
                    ->where('is_featured', true)
                    ->orderBy('created_at', 'desc');

                return $limit === null
                    ? $query->get()
                    : $query->limit($limit)->get();
        }

        return collect();
    }

    /**
     * Fallback: return active parent categories from admin_categories regardless of product count.
     */
    protected function fallbackCategories(int $limit): Collection
    {
        $cats = AdminCategory::whereNull('parent_id')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($cats->isEmpty()) {
            $cats = AdminCategory::orderBy('id', 'asc')->limit($limit)->get();
        }

        return $cats->map(fn ($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'image' => $cat->image_url,
        ]);
    }
}
