<?php

use Illuminate\Support\Facades\Route;
use Beres\Payment\Http\Controllers\PaymentController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Payment Management Routes
    Route::get('payments', [PaymentController::class, 'index'])
        ->name('admin.payments.index');

    Route::post('payments/create', [PaymentController::class, 'createPayment'])
        ->name('admin.payments.create');

    Route::get('payments/transaction/{orderId}', [PaymentController::class, 'transaction'])
        ->name('admin.payments.transaction');

    Route::get('payments/export', [PaymentController::class, 'export'])
        ->name('admin.payments.export');
});
