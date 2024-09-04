<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityAfterCheckJob extends UniqueJob
{
    private Carbon $date;

    public function __construct($date = null)
    {
        $this->date = $date ?? Carbon::now();
    }

    public function handle()
    {
        $maxRounds = 500;

        do {
            $maxRounds--;

            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                SET
                    inventory_movements.quantity_after = quantity_before + quantity_delta,
                    inventory_movements.updated_at = NOW()

                WHERE
                    inventory_movements.type != "stocktake"
                    AND inventory_movements.quantity_after != quantity_before + quantity_delta
                    AND inventory_movements.updated_at BETWEEN ? AND ?;
            ', [$this->date->startOfDay()->toDateTimeLocalString(), $this->date->endOfDay()->toDateTimeLocalString()]);

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
