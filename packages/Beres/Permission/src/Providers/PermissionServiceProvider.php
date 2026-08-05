<?php

namespace Beres\Permission\Providers;

use Beres\Permission\Services\PermissionService;
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
        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-permission');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }

        $permissionsConfig = __DIR__ . '/../Config/permissions.php';
        if (file_exists($permissionsConfig)) {
            $this->mergeConfigFrom($permissionsConfig, 'beres-permission');
        }
    }
}
