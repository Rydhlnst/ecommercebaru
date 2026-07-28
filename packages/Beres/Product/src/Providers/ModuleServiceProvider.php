<?php

namespace Beres\Product\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Product\Models\ProductActivityLog;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        ProductActivityLog::class,
    ];
}
