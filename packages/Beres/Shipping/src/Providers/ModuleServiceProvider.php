<?php

namespace Beres\Shipping\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Shipping\Models\RajaOngkirCache;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        RajaOngkirCache::class,
    ];
}
