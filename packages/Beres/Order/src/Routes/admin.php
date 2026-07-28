<?php

use Illuminate\Support\Facades\Route;
use Beres\Order\Http\Controllers\OrderController;
use Beres\Order\Http\Controllers\OrderApiController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Order Management Routes
    Route::get('orders', [OrderController::class, 'index'])
        ->name('admin.orders.index');

    Route::get('orders/{id}', [OrderController::class, 'show'])
        ->name('admin.orders.show');

    Route::put('orders/{id}/status', [OrderController::class, 'updateStatus'])
        ->name('admin.orders.update_status');

    // Export
    Route::get('orders/export', [OrderController::class, 'export'])
        ->name('admin.orders.export');
});

// API Routes
Route::group(['prefix' => 'api', 'middleware' => ['web', 'api']], function () {
    Route::get('orders', [OrderApiController::class, 'index'])
        ->name('api.orders.index');

    Route::get('orders/{id}', [OrderApiController::class, 'show'])
        ->name('api.orders.show');

    Route::get('orders/{id}/status-history', [OrderApiController::class, 'statusHistory'])
        ->name('api.orders.status_history');

    Route::get('orders/{id}/activity-log', [OrderApiController::class, 'activityLog'])
        ->name('api.orders.activity_log');

    Route::put('orders/{id}/status', [OrderApiController::class, 'updateStatus'])
        ->name('api.orders.update_status');
});
