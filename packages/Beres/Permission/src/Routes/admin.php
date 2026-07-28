<?php

use Illuminate\Support\Facades\Route;
use Beres\Permission\Http\Controllers\PermissionController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Permissions Matrix
    Route::get('permissions', [PermissionController::class, 'index'])
        ->name('admin.permissions.index');

    // User Permissions
    Route::get('users/{userId}/permissions', [PermissionController::class, 'userPermissions'])
        ->name('admin.permissions.user');

    // Check Permission
    Route::post('permissions/check', [PermissionController::class, 'checkPermission'])
        ->name('admin.permissions.check');

    // My Permissions
    Route::get('my-permissions', [PermissionController::class, 'myPermissions'])
        ->name('admin.permissions.my');
});
