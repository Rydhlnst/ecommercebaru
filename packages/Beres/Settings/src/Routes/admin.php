<?php

use Illuminate\Support\Facades\Route;
use Beres\Settings\Http\Controllers\SettingController;

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin', 'auth']], function () {
    // Settings
    Route::get('settings', [SettingController::class, 'index'])
        ->name('admin.settings.index');

    Route::put('settings/store', [SettingController::class, 'updateStore'])
        ->name('admin.settings.store.update');

    Route::put('settings/company', [SettingController::class, 'updateCompany'])
        ->name('admin.settings.company.update');

    Route::put('settings/smtp', [SettingController::class, 'updateSmtp'])
        ->name('admin.settings.smtp.update');
});
