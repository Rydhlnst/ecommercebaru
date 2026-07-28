<?php

use Illuminate\Support\Facades\Route;
use Beres\Shipping\Http\Controllers\ShippingController;

Route::group(['prefix' => 'api/shipping', 'middleware' => ['web']], function () {
    // Province routes
    Route::get('provinces', [ShippingController::class, 'provinces'])
        ->name('api.shipping.provinces');

    // City routes
    Route::get('cities', [ShippingController::class, 'cities'])
        ->name('api.shipping.cities');

    // District routes
    Route::get('districts', [ShippingController::class, 'districts'])
        ->name('api.shipping.districts');

    // Shipping cost calculation
    Route::post('calculate', [ShippingController::class, 'calculateCost'])
        ->name('api.shipping.calculate');

    // Available couriers
    Route::get('couriers', [ShippingController::class, 'couriers'])
        ->name('api.shipping.couriers');

    // Address search
    Route::get('search', [ShippingController::class, 'searchAddress'])
        ->name('api.shipping.search');
});
