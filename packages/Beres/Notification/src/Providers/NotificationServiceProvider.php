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
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'beres-notification');

        $this->registerEventListeners();
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
