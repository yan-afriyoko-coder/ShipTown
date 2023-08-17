<?php

namespace App\Modules\Maintenance\src\Jobs\temp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FixIncorrectQuantityBeforeAndAfterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
            WITH tbl AS (
                SELECT
                    inventory_movements.id as movement_id,
                    inventory_movements.inventory_id as inventory_id,
                    previous_movement.quantity_after as quantity_before_expected,
                    inventory_movements.quantity_before as quantity_before_actual,
                    previous_movement.quantity_after - inventory_movements.quantity_before as quantity_before_delta

                FROM inventory_movements
                INNER JOIN inventory_movements as previous_movement
                 ON inventory_movements.previous_movement_id = previous_movement.id
                WHERE inventory_movements.previous_movement_id IS NOT NULL
                AND inventory_movements.quantity_before != previous_movement.quantity_after
                LIMIT 20
            )

            UPDATE inventory_movements
            INNER JOIN tbl ON
                tbl.movement_id = inventory_movements.id
            SET
                inventory_movements.quantity_before = inventory_movements.quantity_before + tbl.quantity_before_delta,
                inventory_movements.quantity_after = inventory_movements.quantity_after + tbl.quantity_before_delta
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
