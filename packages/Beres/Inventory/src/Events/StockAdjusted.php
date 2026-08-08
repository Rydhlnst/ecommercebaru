<?php

namespace Beres\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAdjusted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly mixed $product,
        public readonly string $action,
        public readonly int $quantity,
        public readonly int $oldQuantity,
        public readonly int $newQuantity
    ) {}
}
