<?php

use Beres\Highlight\Http\Controllers\HomepageHighlightController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['web', 'admin', 'auth'],
], function () {
    Route::get('homepage-manager', [HomepageHighlightController::class, 'index'])
        ->name('admin.homepage_manager.index');

    Route::get('homepage-manager/search', [HomepageHighlightController::class, 'search'])
        ->name('admin.homepage_manager.search');

    Route::post('homepage-manager', [HomepageHighlightController::class, 'store'])
        ->name('admin.homepage_manager.store');

    Route::post('homepage-manager/reorder', [HomepageHighlightController::class, 'reorder'])
        ->name('admin.homepage_manager.reorder');

    Route::delete('homepage-manager/{id}', [HomepageHighlightController::class, 'destroy'])
        ->name('admin.homepage_manager.destroy');
});
