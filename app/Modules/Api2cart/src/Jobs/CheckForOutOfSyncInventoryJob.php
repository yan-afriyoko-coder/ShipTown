<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Helpers\TemporaryTable;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckForOutOfSyncInventoryJob implements ShouldQueue
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
        Api2cartConnection::query()->get()->each(function (Api2cartConnection $connection) {
            $warehouse_ids = Warehouse::query()
                ->withAllTags([$connection->inventory_source_warehouse_tag])
                ->pluck('id');

            $query = Api2cartProductLink::query()
                ->select([
                    'modules_api2cart_product_links.id as api2cart_product_link_id',
                    DB::raw('(
                        modules_api2cart_product_links.api2cart_quantity IS NOT NULL
                        AND modules_api2cart_product_links.api2cart_quantity = SUM(inventory.quantity_available)
                    ) as actual_is_in_sync'),
                ])
                ->leftJoin('inventory', function (JoinClause $join) use ($warehouse_ids) {
                    $join->on('inventory.product_id', '=', 'modules_api2cart_product_links.product_id')
                        ->whereIn('inventory.warehouse_id', $warehouse_ids);
                })
                ->whereRaw('(last_fetched_data IS NOT NULL)')
                ->where(['api2cart_connection_id' => $connection->id])
                ->groupBy(['modules_api2cart_product_links.id']);

            TemporaryTable::create('temp', $query);

            $query = Api2cartProductLink::query()
                ->leftJoin('temp', 'temp.api2cart_product_link_id', '=', 'modules_api2cart_product_links.id')
                ->whereRaw('(is_in_sync IS NULL)')
                ->orWhereRaw('(actual_is_in_sync = 0 AND is_in_sync = 1)');

            $query->limit(1000)->update([
                'is_in_sync' => DB::raw('actual_is_in_sync')
            ]);

            Schema::drop('temp');
        });
    }
}
