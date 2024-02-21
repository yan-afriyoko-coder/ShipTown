<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTotalsByWarehouseTagTableJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated = $this->recalculateTotals();

            Log::debug('Job processing', ['job' => self::class, 'records_updated' => $recordsUpdated]);

            usleep(100000); // 0.1 sec
        } while ($recordsUpdated > 0);
    }

    private function recalculateTotals(): int
    {
        DB::statement("DROP TEMPORARY TABLE IF EXISTS tempTable;");
        DB::statement("DROP TEMPORARY TABLE IF EXISTS tempInventoryTotalsByWarehouseTag;");

        DB::statement("
            CREATE TEMPORARY TABLE tempTable AS
                SELECT
                    inventory_totals_by_warehouse_tag.tag_id,
                    inventory_totals_by_warehouse_tag.product_id,
                    NOW() as calculated_at
                FROM inventory_totals_by_warehouse_tag

                WHERE recalc_required = 1

                LIMIT 100;
        ");

        DB::statement("
            CREATE TEMPORARY TABLE tempInventoryTotalsByWarehouseTag AS
                SELECT
                     tempTable.tag_id as tag_id,
                     tempTable.product_id as product_id,
                     ROUND(inventory.quantity, 2) as quantity,
                     ROUND(inventory.quantity_reserved, 2) as quantity_reserved,
                     ROUND(inventory.quantity_incoming, 2) as quantity_incoming,
                     MAX(inventory.updated_at) as max_inventory_updated_at,
                     tempTable.calculated_at as calculated_at,
                     NOW() as created_at,
                     NOW() as updated_at

                FROM tempTable

                LEFT JOIN taggables
                    ON taggables.tag_id = tempTable.tag_id
                    AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'

                LEFT JOIN inventory
                    ON inventory.product_id = tempTable.product_id
                    AND inventory.warehouse_id = taggables.taggable_id

                GROUP BY tempTable.tag_id, tempTable.product_id, tempTable.calculated_at;
        ");

        return DB::update("
            UPDATE inventory_totals_by_warehouse_tag

            INNER JOIN tempInventoryTotalsByWarehouseTag
                ON tempInventoryTotalsByWarehouseTag.tag_id = inventory_totals_by_warehouse_tag.tag_id
                AND tempInventoryTotalsByWarehouseTag.product_id = inventory_totals_by_warehouse_tag.product_id

            SET
                inventory_totals_by_warehouse_tag.recalc_required = 0,
                inventory_totals_by_warehouse_tag.quantity = tempInventoryTotalsByWarehouseTag.quantity,
                inventory_totals_by_warehouse_tag.quantity_reserved = tempInventoryTotalsByWarehouseTag.quantity_reserved,
                inventory_totals_by_warehouse_tag.quantity_incoming = tempInventoryTotalsByWarehouseTag.quantity_incoming,
                inventory_totals_by_warehouse_tag.max_inventory_updated_at = tempInventoryTotalsByWarehouseTag.max_inventory_updated_at,
                inventory_totals_by_warehouse_tag.calculated_at = tempInventoryTotalsByWarehouseTag.calculated_at,
                inventory_totals_by_warehouse_tag.updated_at = NOW();
        ");
    }
}
