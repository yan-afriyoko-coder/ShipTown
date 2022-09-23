<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Models\Api2cartVariant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckForOutOfSyncQuantity implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        DB::statement('
            UPDATE modules_api2cart_product_links
            SET is_in_sync = 0
            WHERE
                id IN (
                    SELECT product_link_id
                    FROM modules_api2cart_product_quantity_comparison_view
                    WHERE product_link_is_in_sync = 1
                    AND quantity_api2cart != quantity_expected
                )
        ');
    }
}
