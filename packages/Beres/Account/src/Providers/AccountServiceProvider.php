<?php

namespace Beres\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Account\Services\AccountService;

class AccountServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AccountService::class, function ($app) {
            return new AccountService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-account');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
