<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class FetchSqlForUpdatingRMSJob extends UniqueJob
{
    public function handle()
    {
        DB::statement("
            SELECT CONCAT('UPDATE Item SET Quantity=', pm_quantity, ', LastUpdated=getDate() WHERE ItemLookupCode = ''', product_sku,'''; --', warehouse_code)
            FROM `modules_rmsapi_products_quantity_comparison_view`
            WHERE rms_quantity != pm_quantity
            ORDER BY warehouse_code
            LIMIT 500
        ");
    }
}
