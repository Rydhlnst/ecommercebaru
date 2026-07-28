<?php

namespace Beres\Payment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly $order,
        public readonly $transaction,
        public readonly ?string $reason = null
    ) {}
}
