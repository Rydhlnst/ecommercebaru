<?php

namespace App\Http\Controllers;

use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminReview;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

/**
 * Storefront pages for the custom simple products/categories (admin_* tables).
 *
 * Kept as controller actions (not route closures) so that `php artisan route:cache`
 * — used by deploy.sh in production — can serialize these routes.
 */
class CustomCatalogController extends Controller
{
    /**
     * Show a single custom product detail page.
     */
    public function product(string $slug): View
    {
        $product = AdminProduct::with('category', 'images', 'variations')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $recommendations = AdminProduct::with('images', 'category', 'variations')
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->get();

        if ($recommendations->isEmpty()) {
            $recommendations = AdminProduct::with('images', 'category', 'variations')
                ->where('status', 'active')
                ->where('id', '!=', $product->id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            $recommendations = $recommendations->take(5);
        }

        $reviews = collect();
        $reviewCount = 0;
        $averageRating = 0;

        if (Schema::hasTable('admin_reviews')) {
            $reviewQuery = AdminReview::where('product_id', $product->id)->where('is_approved', true);
            $reviewCount = (clone $reviewQuery)->count();
            $averageRating = round((float) ((clone $reviewQuery)->avg('rating') ?? 0), 1);
            $reviews = $reviewQuery->latest()->take(6)->get();
        }

        return view('admin.frontend.product-detail', compact('product', 'recommendations', 'reviews', 'reviewCount', 'averageRating'));
    }

    /**
     * Show a custom category page with its active products.
     */
    public function category(string $slug): View
    {
        $category = AdminCategory::withCount('products')
            ->where('slug', $slug)
            ->firstOrFail();

        $productQuery = AdminProduct::with('images', 'category', 'variations')
            ->where('category_id', $category->id)
            ->where('status', 'active');

        $maxPrice = (clone $productQuery)->max('price') ?? 0;
        $this->applyCatalogFilters($productQuery);
        $products = $productQuery->paginate(12)->withQueryString();

        return view('admin.frontend.category-detail', compact('category', 'products', 'maxPrice'));
    }

    /**
     * Search products in the catalogue.
     */
    public function search(): View
    {
        $query = trim(request()->query('query', ''));

        $productQuery = AdminProduct::with('category', 'images', 'variations')
            ->where('status', 'active');

        if ($query !== '') {
            $productQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    });
            });
        }

        $maxPrice = (clone $productQuery)->max('price') ?? 0;
        $this->applyCatalogFilters($productQuery);
        $products = $productQuery->latest()->paginate(12)->withQueryString();

        return view('shop::search.index', compact('query', 'products', 'maxPrice'));
    }

    /**
     * Apply the shared catalogue filters used by category and search pages.
     */
    private function applyCatalogFilters($query): void
    {
        if (request()->boolean('in_stock')) {
            $query->where(function ($stockQuery) {
                $stockQuery->where('stock', '>', 0)
                    ->orWhereHas('variations', fn ($variationQuery) => $variationQuery->where('stock', '>', 0));
            });
        }

        $minPrice = request()->query('min_price');
        $maxPrice = request()->query('max_price');

        if (is_numeric($minPrice)) {
            $query->where(function ($priceQuery) use ($minPrice) {
                $priceQuery->where('price', '>=', (float) $minPrice)
                    ->orWhereHas('variations', fn ($variationQuery) => $variationQuery->where('price', '>=', (float) $minPrice));
            });
        }

        if (is_numeric($maxPrice)) {
            $query->where(function ($priceQuery) use ($maxPrice) {
                $priceQuery->where('price', '<=', (float) $maxPrice)
                    ->orWhereHas('variations', fn ($variationQuery) => $variationQuery->where('price', '<=', (float) $maxPrice));
            });
        }

        match (request()->query('sort', 'featured')) {
            'price_asc' => $query->orderBy('price')->orderByDesc('created_at'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('created_at'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };
    }
}
