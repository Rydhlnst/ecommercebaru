<?php

use Illuminate\Support\Facades\Route;
use Beres\Payment\Http\Controllers\WebhookController;

// Webhook Routes (no auth required)
Route::post('webhook/midtrans', [WebhookController::class, 'handleMidtrans'])
    ->name('webhook.midtrans');

Route::post('notification/midtrans', [WebhookController::class, 'notification'])
    ->name('notification.midtrans');

// Admin webhook logs (requires auth)
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    Route::get('webhook-logs', [WebhookController::class, 'logs'])
        ->name('admin.webhook-logs');
});
