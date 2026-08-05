<?php

namespace Beres\Highlight\Providers;

use Beres\Highlight\Models\HomepageHighlight;
use Beres\Highlight\Repositories\HomepageHighlightRepository;
use Beres\Highlight\Services\HomepageHighlightService;
use Illuminate\Support\ServiceProvider;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class HighlightServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(HomepageHighlightRepository::class, function ($app) {
            return new HomepageHighlightRepository(new HomepageHighlight);
        });

        $this->app->singleton(HomepageHighlightService::class, function ($app) {
            return new HomepageHighlightService(
                $app->make(HomepageHighlightRepository::class),
                $app->make(ProductRepository::class),
                $app->make(CategoryRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $migrations = __DIR__.'/../Database/Migrations';

        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }

        $views = __DIR__.'/../Resources/views';

        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-highlight');
        }

        $routes = __DIR__.'/../Routes/admin.php';

        if (file_exists($routes)) {
            // Beres Highlight admin route disabled — the standalone /admin Showcase replaces the homepage-manager UI.
            // The HomepageHighlightService is still used by the storefront homepage.
            // $this->loadRoutesFrom($routes);
        }
    }
}
