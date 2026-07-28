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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-dashboard');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');

        $this->publishes([
            __DIR__ . '/../Config/menu.php' => config_path('admin-menu.php'),
        ], 'beres-dashboard-config');
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
