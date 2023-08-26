<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class QuantityDeltaJob implements ShouldQueue, ShouldBeUnique
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
            UPDATE inventory_movements

            SET inventory_movements.quantity_delta = quantity_after - quantity_before

            WHERE  inventory_movements.quantity_delta != quantity_after - quantity_before
                AND (inventory_movements.type = "stocktake" OR inventory_movements.description = "stocktake")
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
