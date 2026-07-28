<?php

namespace Beres\Inventory\Observers;

use Beres\Inventory\Models\StockHistory;
use Webkul\Inventory\Models\InventorySource;

class InventoryObserver
{
    /**
     * Handle the InventorySource "updated" event.
     */
    public function updated(InventorySource $inventorySource): void
    {
        // Log inventory source changes
        $changes = $inventorySource->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            Log::info("Inventory source updated: {$inventorySource->name}", $changes);
        }
    }
}
