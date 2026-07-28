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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-checkout');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
