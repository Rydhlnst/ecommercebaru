<?php

namespace Beres\Order\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Order\Models\OrderStatusHistory;
use Beres\Order\Models\OrderActivityLog;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        OrderStatusHistory::class,
        OrderActivityLog::class,
    ];
}
