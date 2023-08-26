<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InventoryLastMovementIdJob implements ShouldQueue, ShouldBeUnique
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
                        inventory.id as inventory_id,
                        max(inventory_movements.id) as last_movement_id
                    FROM inventory
                    INNER JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.id > IFNULL(inventory.last_movement_id, 0)

                    GROUP BY inventory.id

                    LIMIT 50000
                )
                UPDATE inventory
                INNER JOIN tbl
                 ON tbl.inventory_id = inventory.id
                SET
                    inventory.last_movement_id = tbl.last_movement_id,
                    inventory.updated_at = now();
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
