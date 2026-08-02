<?php

namespace Beres\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use Beres\Payment\Services\PaymentService;
use Beres\Payment\Services\MidtransService;
use Beres\Payment\Repositories\PaymentTransactionRepository;
use Beres\Payment\Repositories\WebhookLogRepository;
use Beres\Payment\Contracts\PaymentTransactionRepositoryInterface;
use Beres\Payment\Contracts\WebhookLogRepositoryInterface;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentTransactionRepositoryInterface::class,
            PaymentTransactionRepository::class
        );

        $this->app->bind(
            WebhookLogRepositoryInterface::class,
            WebhookLogRepository::class
        );

        $this->app->singleton(MidtransService::class, function ($app) {
            return new MidtransService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(MidtransService::class),
                $app->make(PaymentTransactionRepositoryInterface::class),
                $app->make(WebhookLogRepositoryInterface::class)
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
            $this->loadViewsFrom($views, 'beres-payment');
        }

        foreach (['admin.php', 'api.php'] as $routeFile) {
            $r = __DIR__ . '/../Routes/' . $routeFile;
            if (file_exists($r)) {
                $this->loadRoutesFrom($r);
            }
        }

        // Register MidtransSnap into Bagisto's payment_methods registry.
        // Merges — cashondelivery / moneytransfer / etc. stay untouched.
        $paymentMethodsConfig = __DIR__ . '/../Config/payment-methods.php';
        if (file_exists($paymentMethodsConfig)) {
            $this->mergeConfigFrom($paymentMethodsConfig, 'payment_methods');
        }

        $midtransConfig = __DIR__ . '/../Config/midtrans.php';
        if (file_exists($midtransConfig)) {
            $this->publishes([
                $midtransConfig => config_path('midtrans.php'),
            ], 'beres-payment-config');
        }
    }
}
