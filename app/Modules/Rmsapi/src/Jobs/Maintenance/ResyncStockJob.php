<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class ResyncStockJob extends UniqueJob
{
    public function handle()
    {
        DB::statement('
            INSERT INTO inventory_movements (
                type,
                inventory_id,
                product_id,
                warehouse_id,
                quantity_after,
                description,
                user_id,
                created_at,
                updated_at
            )
            SELECT
                "stocktake" as type,
                inventory_id,
                product_id,
                warehouse_id,
                rms_quantity as quantity_after,
                "RMS sync" as description,
                1 as user_id,
                now() as created_at,
                now() updated_at

            FROM modules_rmsapi_products_quantity_comparison_view
            WHERE quantity_delta != 0
        ');
    }
}
