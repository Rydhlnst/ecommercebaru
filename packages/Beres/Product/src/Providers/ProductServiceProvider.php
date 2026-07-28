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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-product');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');

        $this->publishes([
            __DIR__ . '/../Config/system.php' => config_path('system.php'),
        ], 'beres-product-config');
    }
}
