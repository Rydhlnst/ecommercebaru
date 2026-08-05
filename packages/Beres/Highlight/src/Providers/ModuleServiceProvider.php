<?php

namespace Beres\Highlight\Providers;

use Beres\Highlight\Models\HomepageHighlight;
use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [
        HomepageHighlight::class,
    ];
}
