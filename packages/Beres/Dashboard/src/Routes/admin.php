<?php

use Illuminate\Support\Facades\Route;
use Beres\Dashboard\Http\Controllers\DashboardController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard.index');

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics'])
        ->name('admin.dashboard.metrics');

    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])
        ->name('admin.dashboard.chart');

    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])
        ->name('admin.dashboard.clear_cache');

    Route::get('/dashboard/recent-orders', [DashboardController::class, 'recentOrders'])
        ->name('admin.dashboard.recent_orders');

    Route::get('/dashboard/top-products', [DashboardController::class, 'topProducts'])
        ->name('admin.dashboard.top_products');
});
