<?php

namespace Beres\Settings\Providers;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-settings');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');

        // Merge storefront settings ke Bagisto core config
        // Muncul di admin: Configure → Storefront Content
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/system.php',
            'core'
        );
    }
}
