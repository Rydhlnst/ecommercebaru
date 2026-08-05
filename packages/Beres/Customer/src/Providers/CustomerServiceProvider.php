<?php

namespace Beres\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Customer\Services\CustomerService;
use Beres\Customer\Repositories\CustomerActivityLogRepository;
use Beres\Customer\Repositories\CustomerNoteRepository;
use Beres\Customer\Contracts\CustomerActivityLogRepositoryInterface;
use Beres\Customer\Contracts\CustomerNoteRepositoryInterface;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            CustomerActivityLogRepositoryInterface::class,
            CustomerActivityLogRepository::class
        );

        $this->app->bind(
            CustomerNoteRepositoryInterface::class,
            CustomerNoteRepository::class
        );

        $this->app->singleton(CustomerService::class, function ($app) {
            return new CustomerService(
                $app->make(CustomerActivityLogRepositoryInterface::class),
                $app->make(CustomerNoteRepositoryInterface::class)
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
            $this->loadViewsFrom($views, 'beres-customer');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }
    }
}
