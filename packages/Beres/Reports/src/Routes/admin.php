<?php

use Illuminate\Support\Facades\Route;
use Beres\Reports\Http\Controllers\ReportController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Reports Dashboard
    Route::get('reports', [ReportController::class, 'index'])
        ->name('admin.reports.index');

    // Revenue Report
    Route::get('reports/revenue', [ReportController::class, 'revenue'])
        ->name('admin.reports.revenue');

    // Orders Report
    Route::get('reports/orders', [ReportController::class, 'orders'])
        ->name('admin.reports.orders');

    // Products Report
    Route::get('reports/products', [ReportController::class, 'products'])
        ->name('admin.reports.products');

    // Customers Report
    Route::get('reports/customers', [ReportController::class, 'customers'])
        ->name('admin.reports.customers');

    // Export
    Route::get('reports/export/{type}', [ReportController::class, 'export'])
        ->name('admin.reports.export');
});
