<?php

namespace Beres\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Dashboard\Services\DashboardService;
use Beres\Dashboard\Repositories\DashboardCacheRepository;
use Beres\Dashboard\Repositories\ActivityLogRepository;
use Beres\Dashboard\Contracts\DashboardCacheRepositoryInterface;
use Beres\Dashboard\Contracts\ActivityLogRepositoryInterface;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $migrations = __DIR__ . '/../Database/Migrations';
        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }

        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-dashboard');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }

        $menuConfig = __DIR__ . '/../Config/menu.php';
        if (file_exists($menuConfig)) {
            $this->publishes([
                $menuConfig => config_path('admin-menu.php'),
            ], 'beres-dashboard-config');
        }
    }

    /**
     * Register bindings.
     */
    protected function registerBindings(): void
    {
        $this->app->bind(
            DashboardCacheRepositoryInterface::class,
            DashboardCacheRepository::class
        );

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class
        );

        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(DashboardCacheRepositoryInterface::class),
                $app->make(ActivityLogRepositoryInterface::class)
            );
        });
    }
}
