<?php

namespace Beres\Shipping\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Shipping\Services\RajaOngkirService;
use Beres\Shipping\Services\ShippingCalculatorService;
use Beres\Shipping\Repositories\RajaOngkirCacheRepository;
use Beres\Shipping\Contracts\RajaOngkirCacheRepositoryInterface;

class ShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            RajaOngkirCacheRepositoryInterface::class,
            RajaOngkirCacheRepository::class
        );

        $this->app->singleton(RajaOngkirService::class, function ($app) {
            return new RajaOngkirService(
                $app->make(RajaOngkirCacheRepositoryInterface::class)
            );
        });

        $this->app->singleton(ShippingCalculatorService::class, function ($app) {
            return new ShippingCalculatorService(
                $app->make(RajaOngkirService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $migrationsPath = __DIR__ . '/../Database/Migrations';
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        $viewsPath = __DIR__ . '/../Resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'beres-shipping');
        }

        $apiRoutes = __DIR__ . '/../Routes/api.php';
        if (file_exists($apiRoutes)) {
            $this->loadRoutesFrom($apiRoutes);
        }

        // Register RajaOngkir into Bagisto's shipping carriers registry.
        // Merges with flatrate / free — existing carriers stay untouched.
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/carriers.php',
            'carriers'
        );

        $this->publishes([
            __DIR__ . '/../Config/rajaongkir.php' => config_path('rajaongkir.php'),
        ], 'beres-shipping-config');
    }
}
