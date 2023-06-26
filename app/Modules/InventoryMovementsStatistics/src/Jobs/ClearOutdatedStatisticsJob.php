<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class ClearOutdatedStatisticsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement("
            UPDATE `inventory_movements_statistics`

            SET quantity_sold_last_7_days = 0,
                quantity_sold_last_14_days = 0,
                quantity_sold_last_28_days = 0,
                updated_at = now()
            WHERE `last_sold_at` < DATE_SUB(now(), INTERVAL 28 DAY)
            AND (
                IFNULL(`quantity_sold_last_7_days`, 0) != '0'
                OR IFNULL(`quantity_sold_last_14_days`, 0) != '0'
                OR IFNULL(`quantity_sold_last_28_days`, 0) != '0'
            )
        ");
    }
}
