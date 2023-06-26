<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use App\Helpers\TemporaryTable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class EnsureAllRecordsExistsJob implements ShouldQueue
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
        DB::statement('
            INSERT INTO inventory_movements_statistics (
                inventory_id, product_id, warehouse_id, warehouse_code, updated_at, created_at
            )
            SELECT
                id as inventory_id,
                product_id,
                warehouse_id,
                warehouse_code,
                now() as updated_at,
                now() as created_at
            FROM `inventory` i
            WHERE i.id NOT IN (
                SELECT inventory_id FROM inventory_movements_statistics
            )
        ');
    }
}
