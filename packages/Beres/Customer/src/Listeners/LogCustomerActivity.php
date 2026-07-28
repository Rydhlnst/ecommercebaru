<?php

namespace Beres\Customer\Listeners;

use Illuminate\Support\Facades\Log;

class LogCustomerActivity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $customerName = "{$event->customer->first_name} {$event->customer->last_name}";
        $action = class_basename($event);

        Log::info("Customer {$action}: {$customerName} (ID: {$event->customer->id})");
    }
}
