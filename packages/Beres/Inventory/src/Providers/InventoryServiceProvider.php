<?php

namespace Beres\Inventory\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Inventory\Services\InventoryService;
use Beres\Inventory\Repositories\StockHistoryRepository;
use Beres\Inventory\Contracts\StockHistoryRepositoryInterface;

class InventoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            StockHistoryRepositoryInterface::class,
            StockHistoryRepository::class
        );

        $this->app->singleton(InventoryService::class, function ($app) {
            return new InventoryService(
                $app->make(StockHistoryRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-inventory');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
    }
}
