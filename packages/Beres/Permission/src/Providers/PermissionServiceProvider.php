<?php

namespace Beres\Permission\Providers;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-permission');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->mergeConfigFrom(__DIR__ . '/../Config/permissions.php', 'beres-permission');
    }
}
