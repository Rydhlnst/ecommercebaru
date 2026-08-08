<?php

namespace Beres\Product\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly mixed $product,
        public readonly bool $oldStatus,
        public readonly bool $newStatus
    ) {}
}
