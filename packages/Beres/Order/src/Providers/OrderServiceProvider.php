<?php

namespace Beres\Order\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Order\Services\OrderService;
use Beres\Order\Repositories\OrderStatusHistoryRepository;
use Beres\Order\Repositories\OrderActivityLogRepository;
use Beres\Order\Contracts\OrderStatusHistoryRepositoryInterface;
use Beres\Order\Contracts\OrderActivityLogRepositoryInterface;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            OrderStatusHistoryRepositoryInterface::class,
            OrderStatusHistoryRepository::class
        );

        $this->app->bind(
            OrderActivityLogRepositoryInterface::class,
            OrderActivityLogRepository::class
        );

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderStatusHistoryRepositoryInterface::class),
                $app->make(OrderActivityLogRepositoryInterface::class)
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
            $this->loadViewsFrom($views, 'beres-order');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }
    }
}
