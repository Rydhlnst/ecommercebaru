<?php

use Illuminate\Support\Facades\Route;
use Beres\Inventory\Http\Controllers\InventoryController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Inventory Management Routes
    Route::get('inventory', [InventoryController::class, 'index'])
        ->name('admin.inventory.index');

    Route::post('inventory/adjust', [InventoryController::class, 'adjustStock'])
        ->name('admin.inventory.adjust');

    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])
        ->name('admin.inventory.low_stock');

    Route::get('inventory/stats', [InventoryController::class, 'stats'])
        ->name('admin.inventory.stats');

    Route::get('inventory/{productId}/history', [InventoryController::class, 'stockHistory'])
        ->name('admin.inventory.history');
});
