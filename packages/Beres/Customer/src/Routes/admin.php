<?php

use Illuminate\Support\Facades\Route;
use Beres\Customer\Http\Controllers\CustomerController;
use Beres\Customer\Http\Controllers\CustomerApiController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Customer Management Routes
    Route::get('customers', [CustomerController::class, 'index'])
        ->name('admin.customers.index');

    Route::get('customers/{id}', [CustomerController::class, 'show'])
        ->name('admin.customers.show');

    // Customer Notes
    Route::post('customers/{id}/notes', [CustomerController::class, 'addNote'])
        ->name('admin.customers.notes.store');

    Route::put('customers/{customerId}/notes/{noteId}', [CustomerController::class, 'updateNote'])
        ->name('admin.customers.notes.update');

    Route::delete('customers/{customerId}/notes/{noteId}', [CustomerController::class, 'deleteNote'])
        ->name('admin.customers.notes.destroy');

    // Export
    Route::get('customers/export', [CustomerController::class, 'export'])
        ->name('admin.customers.export');
});

// API Routes
Route::group(['prefix' => 'api', 'middleware' => ['web', 'api']], function () {
    Route::get('customers', [CustomerApiController::class, 'index'])
        ->name('api.customers.index');

    Route::get('customers/{id}', [CustomerApiController::class, 'show'])
        ->name('api.customers.show');

    Route::get('customers/{id}/activity-log', [CustomerApiController::class, 'activityLog'])
        ->name('api.customers.activity_log');

    Route::get('customers/{id}/notes', [CustomerApiController::class, 'notes'])
        ->name('api.customers.notes');
});
