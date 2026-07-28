<?php

namespace Beres\Order\Listeners;

use Illuminate\Support\Facades\Log;

class LogOrderActivity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $orderId = $event->order->id;
        $action = class_basename($event);

        Log::info("Order {$action}: #{$orderId}");
    }
}
