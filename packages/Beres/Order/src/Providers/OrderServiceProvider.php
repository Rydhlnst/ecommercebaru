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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-order');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
    }
}
