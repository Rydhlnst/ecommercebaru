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

Route::get('/deploy/chmod', [DeployController::class, 'fixPermissions'])
    ->middleware('throttle:5,1');

Route::get('/deploy/run', [DeployController::class, 'run'])
    ->middleware('throttle:3,1');
