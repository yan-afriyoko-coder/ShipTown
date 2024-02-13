<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIfSyncIsRequiredJob extends UniqueJob
{
    public function handle(): void
    {
        DB::affectingStatement("
            UPDATE modules_magento2msi_inventory_source_items

            LEFT JOIN inventory_totals_by_warehouse_tag
              ON inventory_totals_by_warehouse_tag.id = modules_magento2msi_inventory_source_items.inventory_totals_by_warehouse_tag_id

            SET modules_magento2msi_inventory_source_items.sync_required = (modules_magento2msi_inventory_source_items.quantity != inventory_totals_by_warehouse_tag.quantity_available),
                modules_magento2msi_inventory_source_items.updated_at = NOW()

            WHERE modules_magento2msi_inventory_source_items.id in (
                SELECT ID FROM (
                    SELECT modules_magento2msi_inventory_source_items.id

                    FROM modules_magento2msi_inventory_source_items

                    WHERE modules_magento2msi_inventory_source_items.sync_required IS NULL

                    LIMIT 500
                ) as tbl
            );
       ");
    }
}
