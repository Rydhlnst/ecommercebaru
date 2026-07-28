<?php

namespace Beres\Dashboard\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Dashboard\Models\DashboardCache;
use Beres\Dashboard\Models\ActivityLog;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        DashboardCache::class,
        ActivityLog::class,
    ];
}
