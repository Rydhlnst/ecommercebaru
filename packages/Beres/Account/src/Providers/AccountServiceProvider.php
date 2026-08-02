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
        $viewsPath = __DIR__ . '/../Resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'beres-account');
        }

        $routes = __DIR__ . '/../Routes/web.php';
        if (file_exists($routes)) {
            $this->loadRoutesFrom($routes);
        }
    }
}
