<?php

namespace App\Http\Controllers;

use App\Models\AdminCategory;
use App\Models\AdminProduct;
use Illuminate\View\View;

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

        return view('admin.frontend.product-detail', compact('product'));
    }

    /**
     * Show a custom category page with its active products.
     */
    public function category(string $slug): View
    {
        $category = AdminCategory::withCount('products')
            ->where('slug', $slug)
            ->firstOrFail();

        $products = AdminProduct::with('images')
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->paginate(12);

        return view('admin.frontend.category-detail', compact('category', 'products'));
    }
}
