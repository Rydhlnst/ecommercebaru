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
        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-settings');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }

        // Merge storefront settings ke Bagisto core config
        // Muncul di admin: Configure → Storefront Content
        $systemConfig = __DIR__ . '/../Config/system.php';
        if (file_exists($systemConfig)) {
            $this->mergeConfigFrom($systemConfig, 'core');
        }
    }
}
