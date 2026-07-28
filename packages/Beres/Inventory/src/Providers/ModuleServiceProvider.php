<?php

namespace Beres\Inventory\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Inventory\Models\StockHistory;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        StockHistory::class,
    ];
}
