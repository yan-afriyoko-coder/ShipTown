<?php

namespace App\Modules\MagentoApi\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Tags\Tag;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureCorrectRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement('
            UPDATE modules_magento2api_products
            SET base_prices_raw_import = NULL
            WHERE magento_price IS NULL
              AND base_prices_raw_import IS NOT NULL
        ');

        DB::statement('
            UPDATE modules_magento2api_products
            SET special_prices_raw_import = NULL
            WHERE magento_sale_price IS NULL
              AND special_prices_raw_import IS NOT NULL
        ');
    }
}
