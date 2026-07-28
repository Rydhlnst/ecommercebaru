<?php

namespace Beres\Dashboard\Listeners;

use Beres\Dashboard\Events\DashboardCacheCleared;
use Beres\Dashboard\Services\DashboardService;

class ClearDashboardCache
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(DashboardCacheCleared $event): void
    {
        $this->dashboardService->clearCache();
    }
}
