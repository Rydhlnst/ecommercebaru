<?php

namespace Beres\Product\Listeners;

use Beres\Product\Models\ProductActivityLog;
use Illuminate\Support\Facades\Log;

class LogProductActivity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $productName = class_basename($event->product);
        $action = class_basename($event);

        Log::info("Product {$action}: {$event->product->name} (ID: {$event->product->id})");
    }
}
