<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class RepublishWebhooksForDiscrepencies extends UniqueJob
{
    public function handle()
    {
        DB::statement('
            INSERT INTO modules_webhooks_pending_webhooks (model_class, model_id, message, created_at, updated_at)
            SELECT
                ? as model_class,
                modules_rmsapi_products_quantity_comparison_view.inventory_id as model_id,
                "{}" as message,
                now() as created_at,
                now() as updated_at
            FROM modules_rmsapi_products_quantity_comparison_view

                     LEFT JOIN modules_webhooks_pending_webhooks
                               ON modules_webhooks_pending_webhooks.model_id = modules_rmsapi_products_quantity_comparison_view.inventory_id
                                   AND modules_webhooks_pending_webhooks.model_class = ?
                                   AND modules_webhooks_pending_webhooks.reserved_at IS NULL

            WHERE quantity_delta != 0
              AND modules_webhooks_pending_webhooks.id is null;
        ', [Inventory::class, Inventory::class]);
    }
}
