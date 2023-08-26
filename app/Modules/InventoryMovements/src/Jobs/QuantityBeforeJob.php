<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class QuantityBeforeJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public function uniqueId(): string
    {
        return get_class($this);
    }

    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
            WITH tbl AS (
                SELECT
                    inventory_movements.created_at as created_at,
                    inventory_movements.id as movement_id,
                    inventory_movements.description as description,
                    inventory_movements.inventory_id as inventory_id,

                    previous_movement.quantity_after as quantity_before_expected,

                    inventory_movements.quantity_before as quantity_before_actual,
                    previous_movement.quantity_after - inventory_movements.quantity_before as quantity_before_delta,
                    inventory_movements.quantity_after as quantity_after_actual,
                    CASE WHEN inventory_movements.description = "stocktake"
                        THEN inventory_movements.quantity_after
                        ELSE inventory_movements.quantity_after + (previous_movement.quantity_after - inventory_movements.quantity_before)
                        END AS quantity_after_expected,
                    inventory_movements.quantity_delta as quantity_delta_actual,
                    CASE WHEN inventory_movements.description = "stocktake"
                        THEN inventory_movements.quantity_delta - (previous_movement.quantity_after - inventory_movements.quantity_before)
                        ElSE inventory_movements.quantity_delta
                        END AS quantity_delta_expected

                FROM inventory_movements
                INNER JOIN inventory_movements as previous_movement
                 ON previous_movement.id = inventory_movements.previous_movement_id
                 AND inventory_movements.quantity_before != previous_movement.quantity_after
                LIMIT 200
            )

            UPDATE inventory_movements
            INNER JOIN tbl ON
                tbl.movement_id = inventory_movements.id
            SET
                inventory_movements.quantity_before = tbl.quantity_before_expected,
                inventory_movements.quantity_delta = tbl.quantity_delta_expected,
                inventory_movements.quantity_after = tbl.quantity_after_expected;
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
