<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Events\RecalculateInventoryRequestEvent;
use Illuminate\Support\Facades\DB;

class RecalculateInventoryRequestEventListener
{
    public function handle(RecalculateInventoryRequestEvent $event)
    {
        DB::affectingStatement("
            UPDATE inventory_totals_by_warehouse_tag
            SET inventory_totals_by_warehouse_tag.calculated_at = null
            WHERE inventory_totals_by_warehouse_tag.product_id IN (SELECT DISTINCT product_id FROM inventory WHERE id IN (
                ".$event->inventoryRecordsIds->implode(',') ."
            ))
        ");
    }
}
