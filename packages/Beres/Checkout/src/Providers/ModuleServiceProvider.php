<?php

namespace Beres\Checkout\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Checkout\Models\CheckoutSession;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        CheckoutSession::class,
    ];
}
