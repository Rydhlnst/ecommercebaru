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
        $migrations = __DIR__ . '/../Database/Migrations';
        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }

        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-inventory');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }
    }
}
