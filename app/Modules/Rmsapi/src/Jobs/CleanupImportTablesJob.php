<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Modules\Rmsapi\src\Models\RmsapiShippingImports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CleanupImportTablesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        // remove duplicates from products import table, keep the latest only
        DB::statement('
            DELETE FROM modules_rmsapi_products_imports WHERE ID NOT IN (
                SELECT id FROM (
                    SELECT max(id) as id
                    FROM `modules_rmsapi_products_imports`
                    GROUP BY product_id, warehouse_id
                ) as tbl)
        ');

        // Cleanup products import table
        RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);

        RmsapiProductImport::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();

        // Cleanup sales imports
        RmsapiSaleImport::query()
            ->whereNull('processed_at')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);

        RmsapiSaleImport::query()
            ->where('processed_at', '<', now()->subDays(7))
            ->forceDelete();

        // Cleanup shipping imports
        RmsapiShippingImports::query()
            ->where('created_at', '<', now()->subDays(7))
            ->forceDelete();

        return true;
    }
}
