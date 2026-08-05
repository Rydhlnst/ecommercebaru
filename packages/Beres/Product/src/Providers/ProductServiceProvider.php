<?php

namespace Beres\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Product\Services\ProductService;
use Beres\Product\Repositories\ProductActivityLogRepository;
use Beres\Product\Contracts\ProductActivityLogRepositoryInterface;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductActivityLogRepositoryInterface::class,
            ProductActivityLogRepository::class
        );

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductActivityLogRepositoryInterface::class)
            );
        });
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
            $this->loadViewsFrom($views, 'beres-product');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }

        $systemConfig = __DIR__ . '/../Config/system.php';
        if (file_exists($systemConfig)) {
            $this->publishes([
                $systemConfig => config_path('system.php'),
            ], 'beres-product-config');
        }
    }
}
