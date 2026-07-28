<?php

namespace Beres\Shipping\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShippingCostCalculated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly int $originCityId,
        public readonly int $destinationCityId,
        public readonly int $weight,
        public readonly array $costs
    ) {}
}
