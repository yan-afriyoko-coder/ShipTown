<?php

namespace App\Modules\Rmsapi\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateProductIdsOnSalesImportsTableJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        retry(3, function () {
            DB::statement('
                UPDATE modules_rmsapi_sales_imports
                LEFT JOIN products_aliases
                  ON modules_rmsapi_sales_imports.sku = products_aliases.alias

                SET modules_rmsapi_sales_imports.product_id = products_aliases.product_id

                WHERE modules_rmsapi_sales_imports.product_id IS NULL

                AND modules_rmsapi_sales_imports.id in (
                    SELECT id FROM (
                      SELECT ID from modules_rmsapi_sales_imports
                      WHERE product_id is null and processed_at is null
                      limit 5000
                    ) as tbl
                )
            ');
        }, 1000);

        return true;
    }
}
