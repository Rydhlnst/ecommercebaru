<?php

namespace Beres\Checkout\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Checkout\Services\CheckoutService;
use Beres\Checkout\Repositories\CheckoutSessionRepository;
use Beres\Checkout\Contracts\CheckoutSessionRepositoryInterface;

class CheckoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            CheckoutSessionRepositoryInterface::class,
            CheckoutSessionRepository::class
        );

        $this->app->singleton(CheckoutService::class);
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
            $this->loadViewsFrom($views, 'beres-checkout');
        }

        $routes = __DIR__ . '/../Routes/web.php';
        if (file_exists($routes)) {
            $this->loadRoutesFrom($routes);
        }
    }
}
