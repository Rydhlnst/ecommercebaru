<?php

namespace Beres\Inventory\Listeners;

use Illuminate\Support\Facades\Log;

class LogStockChange
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $productName = $event->product->name;
        $action = $event->action;

        Log::info("Stock {$action}: {$productName} ({$event->oldQuantity} → {$event->newQuantity})");
    }
}
