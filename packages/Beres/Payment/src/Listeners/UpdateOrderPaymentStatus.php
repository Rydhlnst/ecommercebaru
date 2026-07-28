<?php

namespace Beres\Payment\Listeners;

use Illuminate\Support\Facades\Log;

class UpdateOrderPaymentStatus
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $orderId = $event->order->id;
        $status = $event->transaction->status ?? 'unknown';

        Log::info("Payment status updated for order #{$orderId}: {$status}");
    }
}
