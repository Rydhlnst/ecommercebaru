<?php

namespace Beres\Customer\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly mixed $customer,
        public readonly bool $oldStatus,
        public readonly bool $newStatus
    ) {}
}
