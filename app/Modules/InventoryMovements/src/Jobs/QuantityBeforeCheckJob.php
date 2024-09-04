<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityBeforeCheckJob extends UniqueJob
{
    private Carbon $date;

    public function __construct($date = null)
    {
        $this->date = $date ?? Carbon::now();
    }

    public function handle()
    {
        do {
            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                INNER JOIN inventory_movements as previous_movement
                    ON inventory_movements.inventory_id = previous_movement.inventory_id
                    AND inventory_movements.sequence_number - 1 = previous_movement.sequence_number
                    AND (
                        inventory_movements.quantity_before != previous_movement.quantity_after
                        OR inventory_movements.occurred_at < previous_movement.occurred_at
                    )

                SET inventory_movements.sequence_number = null

                WHERE inventory_movements.occurred_at BETWEEN ? AND ?;
            ', [$this->date->toDateTimeLocalString(), $this->date->addDay()->toDateTimeLocalString()]);

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0);
    }
}
