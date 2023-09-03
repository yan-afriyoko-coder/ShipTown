<?php

namespace App\Modules\Maintenance\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyInventoryMovementsToNewTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement('
            INSERT INTO inventory_movements_new (
                id,
                inventory_id,
                warehouse_code,
                product_id,
                type,
                is_first_movement,
                warehouse_id,
                user_id,
                quantity_delta,
                quantity_before,
                quantity_after,
                description,
                custom_unique_reference_id,
                previous_movement_id,
                created_at,
                updated_at
            )
            SELECT
                inventory_movements.id,
                inventory_movements.inventory_id,
                warehouses.code,
                inventory_movements.product_id,
                inventory_movements.type,
                inventory_movements.is_first_movement,
                inventory_movements.warehouse_id,
                inventory_movements.user_id,
                inventory_movements.quantity_delta,
                inventory_movements.quantity_before,
                inventory_movements.quantity_after,
                inventory_movements.description,
                inventory_movements.custom_unique_reference_id,
                inventory_movements.previous_movement_id,
                inventory_movements.created_at,
                inventory_movements.updated_at
            FROM inventory_movements

            LEFT JOIN inventory_movements_new
              ON inventory_movements_new.id = inventory_movements.id

            LEFT JOIN warehouses
              ON warehouses.id = inventory_movements.warehouse_id

            WHERE inventory_movements_new.id IS NULL

            LIMIT 100
        ');
    }
}
