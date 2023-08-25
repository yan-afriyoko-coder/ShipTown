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
                    inventory_movements.id as movement_id,
                    previous_movement.quantity_after as quantity_before_expected

                FROM inventory_movements
                INNER JOIN inventory_movements as previous_movement
                 ON previous_movement.id = inventory_movements.previous_movement_id

                WHERE previous_movement.quantity_after != inventory_movements.quantity_before
                    AND inventory_movements.previous_movement_id IS NOT NULL
                LIMIT 500
            )

            UPDATE inventory_movements
            INNER JOIN tbl ON
                tbl.movement_id = inventory_movements.id
            SET
                inventory_movements.quantity_before = tbl.quantity_before_expected,
                inventory_movements.updated_at = now()
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
