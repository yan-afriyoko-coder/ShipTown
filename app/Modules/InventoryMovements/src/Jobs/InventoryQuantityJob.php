<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InventoryQuantityJob implements ShouldQueue, ShouldBeUnique
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
                UPDATE inventory

                INNER JOIN inventory_movements
                  ON inventory_movements.id = inventory.last_movement_id
                  AND inventory_movements.quantity_after != inventory.quantity

                SET
                    inventory.quantity = inventory_movements.quantity_after
                  , inventory.updated_at = now();
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
