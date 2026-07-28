<?php

use Illuminate\Support\Facades\Route;
use Beres\Account\Http\Controllers\AccountController;

Route::group(['prefix' => 'account', 'middleware' => ['web', 'auth']], function () {
    // Dashboard
    Route::get('/', [AccountController::class, 'index'])
        ->name('shop.customer.account.index');

    // Profile
    Route::get('profile', [AccountController::class, 'profile'])
        ->name('shop.customer.account.profile');

    Route::put('profile', [AccountController::class, 'updateProfile'])
        ->name('shop.customer.account.profile.update');

    // Addresses
    Route::get('addresses', [AccountController::class, 'addresses'])
        ->name('shop.customer.account.addresses');

    Route::post('addresses', [AccountController::class, 'addAddress'])
        ->name('shop.customer.account.addresses.store');

    Route::put('addresses/{addressId}', [AccountController::class, 'updateAddress'])
        ->name('shop.customer.account.addresses.update');

    Route::delete('addresses/{addressId}', [AccountController::class, 'deleteAddress'])
        ->name('shop.customer.account.addresses.delete');

    // Orders
    Route::get('orders', [AccountController::class, 'orders'])
        ->name('shop.customer.account.orders');

    Route::get('orders/{orderId}', [AccountController::class, 'orderDetail'])
        ->name('shop.customer.account.orders.detail');

    // Wishlist
    Route::get('wishlist', [AccountController::class, 'wishlist'])
        ->name('shop.customer.account.wishlist');

    Route::post('wishlist', [AccountController::class, 'addToWishlist'])
        ->name('shop.customer.account.wishlist.add');

    Route::delete('wishlist/{productId}', [AccountController::class, 'removeFromWishlist'])
        ->name('shop.customer.account.wishlist.remove');

    // Change Password
    Route::get('change-password', [AccountController::class, 'changePassword'])
        ->name('shop.customer.account.change_password');

    Route::put('change-password', [AccountController::class, 'updatePassword'])
        ->name('shop.customer.account.change_password.update');
});
