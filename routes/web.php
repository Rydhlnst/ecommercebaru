<?php

use App\Http\Controllers\DeployController;

/*
|--------------------------------------------------------------------------
| Deploy Helper Routes (cPanel deployment)
|--------------------------------------------------------------------------
|
| Secret URL routes for triggering artisan commands via browser.
| Protected by DEPLOY_SECRET key from .env.
|
| Usage: /deploy?key=YOUR_SECRET&action=ACTION
|
| Available actions:
|   status          — Health check
|   seed-all        — Full fresh seed (migrate:fresh + all seeders + index)
|   seed-categories — Seed categories only
|   seed-products   — Seed products only
|   seed-theme      — Seed homepage theme only
|   cache-clear     — Clear all caches
|   index-rebuild   — Rebuild search index
|   storage-link    — Create storage symlink
|   chmod           — Fix storage permissions
|   run&cmd=xxx     — Run whitelisted artisan command
|
*/

Route::get('/deploy', [DeployController::class, 'index'])
    ->middleware('throttle:10,1');

Route::get('/deploy/seed-all', [DeployController::class, 'seedAll'])
    ->middleware('throttle:2,1');

Route::get('/deploy/seed-categories', [DeployController::class, 'seedCategories'])
    ->middleware('throttle:5,1');

Route::get('/deploy/seed-products', [DeployController::class, 'seedProducts'])
    ->middleware('throttle:5,1');

Route::get('/deploy/seed-theme', [DeployController::class, 'seedTheme'])
    ->middleware('throttle:5,1');

Route::get('/deploy/cache-clear', [DeployController::class, 'cacheClear'])
    ->middleware('throttle:10,1');

Route::get('/deploy/index-rebuild', [DeployController::class, 'indexRebuild'])
    ->middleware('throttle:5,1');

Route::get('/deploy/storage-link', [DeployController::class, 'storageLink'])
    ->middleware('throttle:5,1');

Route::get('/deploy/storage-sync', [DeployController::class, 'storageSync'])
    ->middleware('throttle:5,1');

Route::get('/deploy/chmod', [DeployController::class, 'fixPermissions'])
    ->middleware('throttle:5,1');

Route::get('/deploy/run', [DeployController::class, 'run'])
    ->middleware('throttle:3,1');

/*
|--------------------------------------------------------------------------
| Frontend Product/Category Routes for Admin Products
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CustomCatalogController;

Route::get('/product/{slug}', [CustomCatalogController::class, 'product'])
    ->name('shop.admin_product.show');

Route::get('/category/{slug}', [CustomCatalogController::class, 'category'])
    ->name('shop.admin_category.show');

Route::get('/search', [CustomCatalogController::class, 'search'])
    ->name('shop.search.index');

/*
|--------------------------------------------------------------------------
| Custom Session Cart (AdminProduct catalogue — independent of Bagisto cart)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CartController;

Route::get('/api/cart', [CartController::class, 'show'])->name('cart.show');
Route::get('/api/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/faq', function () {
    $faqs = Faq::where('is_active', true)->orderBy('sort_order')->latest()->get();

    return view('shop::faq', compact('faqs'));
})->name('shop.faq');

/*
|--------------------------------------------------------------------------
| Blog Frontend Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\BlogFrontendController;

Route::get('/blogs', [BlogFrontendController::class, 'index'])->name('shop.blog.index');
Route::get('/blog', [BlogFrontendController::class, 'index']);
Route::get('/blogs/news', [BlogFrontendController::class, 'index']);
Route::get('/blog/{slug}', [BlogFrontendController::class, 'show'])->name('shop.blog.show');

/*
|--------------------------------------------------------------------------
| Blog API Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\BlogApiController;

Route::prefix('api')->group(function () {
    // Public blog API
    Route::get('/blogs', [BlogApiController::class, 'index'])->name('api.blogs.index');
    Route::get('/blogs/{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
    Route::get('/blog/categories', [BlogApiController::class, 'categories'])->name('api.blogs.categories');

    // Admin blog API (requires authentication)
    Route::prefix('admin')->name('api.admin.')->middleware(['web', 'admin.auth'])->group(function () {
        Route::get('/blogs', [BlogApiController::class, 'adminIndex'])->name('blogs.index');
        Route::post('/blogs', [BlogApiController::class, 'store'])->name('blogs.store');
        Route::put('/blogs/{post}', [BlogApiController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{post}', [BlogApiController::class, 'destroy'])->name('blogs.destroy');
        Route::post('/blogs/upload-image', [BlogApiController::class, 'uploadImage'])->name('blogs.uploadImage');
        Route::get('/blog/categories', [BlogApiController::class, 'adminCategories'])->name('blogs.categories.index');
        Route::post('/blog/categories', [BlogApiController::class, 'storeCategory'])->name('blogs.categories.store');
        Route::put('/blog/categories/{category}', [BlogApiController::class, 'updateCategory'])->name('blogs.categories.update');
        Route::delete('/blog/categories/{category}', [BlogApiController::class, 'destroyCategory'])->name('blogs.categories.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Panel Routes (admin CRUD)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminShowcaseController;
use App\Models\Faq;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('/categories/clear-all', [AdminCategoryController::class, 'clearAll'])->name('categories.clearAll');
        Route::resource('categories', AdminCategoryController::class)->parameters([
            'categories' => 'category',
        ]);

        Route::post('/products/clear-all', [AdminProductController::class, 'clearAll'])->name('products.clearAll');
        Route::resource('products', AdminProductController::class)->parameters([
            'products' => 'product',
        ]);

        Route::get('/showcase', [AdminShowcaseController::class, 'index'])->name('showcase.index');
        Route::post('/showcase', [AdminShowcaseController::class, 'store'])->name('showcase.store');

        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'destroy']);
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
        Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
        Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
        Route::post('/blog/upload-image', [AdminBlogController::class, 'uploadImage'])->name('blog.uploadImage');
        Route::post('/blog/categories', [AdminBlogController::class, 'storeCategory'])->name('blog.storeCategory');
        Route::put('/blog/categories/{category}', [AdminBlogController::class, 'updateCategory'])->name('blog.updateCategory');
        Route::delete('/blog/categories/{category}', [AdminBlogController::class, 'destroyCategory'])->name('blog.destroyCategory');

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::resource('faqs', AdminFaqController::class)->parameters([
            'faqs' => 'faq',
        ]);

        Route::get('/settings/integrations', [AdminSettingController::class, 'integrations'])->name('settings.integrations');
        Route::post('/settings/integrations', [AdminSettingController::class, 'updateIntegrations'])->name('settings.integrations.update');
        Route::get('/settings/policy', [AdminSettingController::class, 'policy'])->name('settings.policy');
        Route::post('/settings/policy', [AdminSettingController::class, 'updatePolicy'])->name('settings.policy.update');
        Route::get('/settings/store', [AdminSettingController::class, 'store'])->name('settings.store');
        Route::post('/settings/store', [AdminSettingController::class, 'updateStore'])->name('settings.store.update');
    });
});

/*
|--------------------------------------------------------------------------
| Public Storage Fallback Route (cPanel compatibility)
|--------------------------------------------------------------------------
| Ensures that images uploaded in storage/app/public are served directly
| even when symlink (php artisan storage:link) is disabled or broken on cPanel.
*/
Route::get('/storage/{path}', function (string $path) {
    // 1. Try storage/app/public/$path
    $storagePath = storage_path('app/public/'.$path);
    if (file_exists($storagePath) && is_file($storagePath)) {
        $mime = @mime_content_type($storagePath) ?: 'application/octet-stream';

        return response()->file($storagePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    // 2. Try public_path('storage/' . $path)
    $pubStoragePath = public_path('storage/'.$path);
    if (file_exists($pubStoragePath) && is_file($pubStoragePath)) {
        $mime = @mime_content_type($pubStoragePath) ?: 'application/octet-stream';

        return response()->file($pubStoragePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    // 3. Try public_path($path)
    $pubPath = public_path($path);
    if (file_exists($pubPath) && is_file($pubPath)) {
        $mime = @mime_content_type($pubPath) ?: 'application/octet-stream';

        return response()->file($pubPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');
