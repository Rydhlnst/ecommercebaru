<?php

namespace Beres\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $views = __DIR__ . '/../Resources/views';
        if (is_dir($views)) {
            $this->loadViewsFrom($views, 'beres-notification');
        }

        $this->applyMailSettings();

        $this->registerEventListeners();
    }

    /**
     * Override runtime mail config with values from admin dashboard
     * (Configure → Storefront → Email). Admin fields win over .env.
     * Only switches Laravel's mail driver to `resend` if an API key is set.
     */
    protected function applyMailSettings(): void
    {
        // core() may not be bound in some early bootstrap paths (e.g. artisan package:discover).
        if (! function_exists('core')) {
            return;
        }

        try {
            $apiKey    = (string) core()->getConfigData('beres_storefront.email.api_key');
            $fromEmail = (string) core()->getConfigData('beres_storefront.email.from_email');
            $fromName  = (string) core()->getConfigData('beres_storefront.email.from_name');
        } catch (\Throwable $e) {
            return;
        }

        if ($apiKey !== '') {
            config(['services.resend.key' => $apiKey]);

            // Only switch the mail driver if the Resend transport package is installed.
            // Without the factory class, Laravel would throw when trying to resolve the mailer.
            if (class_exists(\Resend\Laravel\Transport\ResendTransportFactory::class)) {
                config(['mail.default' => 'resend']);
            }
        }

        if ($fromEmail !== '') {
            config(['mail.from.address' => $fromEmail]);
        }
        if ($fromName !== '') {
            config(['mail.from.name' => $fromName]);
        }
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        Event::listen(
            \Beres\Notification\Events\OrderCreatedNotification::class,
            [\Beres\Notification\Listeners\SendOrderNotification::class, 'handleOrderCreated']
        );

        Event::listen(
            \Beres\Notification\Events\PaymentSuccessNotification::class,
            [\Beres\Notification\Listeners\SendOrderNotification::class, 'handlePaymentSuccess']
        );

        Event::listen(
            \Beres\Notification\Events\OrderShippedNotification::class,
            [\Beres\Notification\Listeners\SendOrderNotification::class, 'handleOrderShipped']
        );

        Event::listen(
            \Beres\Notification\Events\OrderCompletedNotification::class,
            [\Beres\Notification\Listeners\SendOrderNotification::class, 'handleOrderCompleted']
        );
    }
}
