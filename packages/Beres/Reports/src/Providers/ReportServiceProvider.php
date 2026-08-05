<?php

namespace Beres\Reports\Providers;

use Beres\Reports\Services\ReportService;
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
        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-reports');
        }

        $routes = __DIR__ . '/../Routes/admin.php';
        if (file_exists($routes)) {
            // Beres admin module disabled — consolidated onto the standalone /admin panel.
            // $this->loadRoutesFrom($routes);
        }
    }
}
