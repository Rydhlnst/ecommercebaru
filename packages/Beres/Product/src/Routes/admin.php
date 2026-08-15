<?php

use Illuminate\Support\Facades\Route;
use Beres\Product\Http\Controllers\ProductController;
use Beres\Product\Http\Controllers\ProductApiController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Bulk Actions
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])
        ->name('admin.products.bulk_action');

    // Import/Export
    Route::post('products/import', [ProductController::class, 'import'])
        ->name('admin.products.import');

    Route::get('products/export', [ProductController::class, 'export'])
        ->name('admin.products.export');
});

// API Routes
Route::group(['prefix' => 'api', 'middleware' => ['web', 'api']], function () {
    Route::get('products', [ProductApiController::class, 'index'])
        ->name('api.products.index');

    Route::get('products/{id}', [ProductApiController::class, 'show'])
        ->name('api.products.show');

    Route::get('products/{id}/activity-log', [ProductApiController::class, 'activityLog'])
        ->name('api.products.activity_log');
});
