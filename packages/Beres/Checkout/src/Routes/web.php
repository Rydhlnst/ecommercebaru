<?php

use Illuminate\Support\Facades\Route;
use Beres\Checkout\Http\Controllers\CheckoutController;

Route::group(['prefix' => 'checkout', 'middleware' => ['web']], function () {
    // Checkout page
    Route::get('/', [CheckoutController::class, 'index'])
        ->name('shop.checkout.index');

    // Calculate shipping
    Route::post('calculate-shipping', [CheckoutController::class, 'calculateShipping'])
        ->name('shop.checkout.calculate_shipping');

    // Get order summary
    Route::get('summary', [CheckoutController::class, 'getSummary'])
        ->name('shop.checkout.summary');

    // Create checkout session
    Route::post('session', [CheckoutController::class, 'createSession'])
        ->name('shop.checkout.session.store');

    // JavaScript-free WhatsApp fallback: creates the order and redirects directly.
    Route::post('whatsapp', [CheckoutController::class, 'whatsappOrder'])
        ->name('shop.checkout.whatsapp');

    // Place order
    Route::post('place-order', [CheckoutController::class, 'placeOrder'])
        ->name('shop.checkout.place_order');

    // Checkout success
    Route::get('success', [CheckoutController::class, 'success'])
        ->name('shop.checkout.success');
});
