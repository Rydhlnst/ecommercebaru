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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-payment');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Register MidtransSnap into Bagisto's payment_methods registry.
        // Merges — cashondelivery / moneytransfer / etc. stay untouched.
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/payment-methods.php',
            'payment_methods'
        );

        $this->publishes([
            __DIR__ . '/../Config/midtrans.php' => config_path('midtrans.php'),
        ], 'beres-payment-config');
    }
}
