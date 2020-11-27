<?php

namespace App\Jobs\Maintenance;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class RecalculateOrdersReservedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function withPrefix($tableName)
    {
        return  config('database.connections.mysql.prefix').$tableName;
    }
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $locationId = 999;

        $quantityToShipTotals = OrderProduct::whereStatusCodeIn(OrderStatus::getOpenStatuses())
            ->addSelect([
                'order_products.product_id',
                DB::raw('sum(quantity_to_ship) as total_quantity_to_ship'),
            ])
            ->groupBy(['order_products.product_id']);

        $productQuantityReservedDifferences = Product::query()
            ->select([
                'products.id',
                DB::raw($locationId.' as location_id'),
                DB::raw('ceil('.DB::getTablePrefix().'inventory.quantity_reserved) as quantity_reserved_actual'),
                'product_reserved_totals.total_quantity_to_ship as quantity_reserved_expected',
            ])
            ->leftJoin('inventory', function ($join) use ($locationId) {
                $join->on('inventory.product_id', '=', 'products.id');
                $join->on('inventory.location_id', '=', DB::raw($locationId));
            })
            ->leftJoinSub(
                $quantityToShipTotals,
                'product_reserved_totals',
                'product_reserved_totals.product_id',
                '=',
                'products.id'
            )
            ->where(DB::raw('IFNULL('.DB::getTablePrefix().'inventory.quantity_reserved, 0)'), '!=', DB::raw('IFNULL(`'.DB::getTablePrefix().'product_reserved_totals`.`total_quantity_to_ship`, 0)'))
            ->limit(1000)
            ->get();

        foreach ($productQuantityReservedDifferences as $product) {
            activity()->on($product)->log('Incorrect quantity reserved detected');
            Inventory::query()->updateOrCreate([
                'product_id' => $product->id,
                'location_id' => $locationId,
            ], [
                'quantity_reserved' => $product->quantity_reserved_expected ?? 0
            ]);
        }
    }
}
