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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-shipping');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        $this->publishes([
            __DIR__ . '/../Config/rajaongkir.php' => config_path('rajaongkir.php'),
        ], 'beres-shipping-config');
    }
}
