<?php

namespace Beres\Payment\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Payment\Models\PaymentTransaction;
use Beres\Payment\Models\WebhookLog;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        PaymentTransaction::class,
        WebhookLog::class,
    ];
}
