<?php

namespace Beres\Reports\Providers;

use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-reports');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
    }
}
