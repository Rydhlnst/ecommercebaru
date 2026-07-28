<?php

namespace Beres\Customer\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Beres\Customer\Models\CustomerActivityLog;
use Beres\Customer\Models\CustomerNote;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        CustomerActivityLog::class,
        CustomerNote::class,
    ];
}
