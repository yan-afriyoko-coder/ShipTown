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
        $prefix = config('database.connections.mysql.prefix');

        $expectedReservedQuantities = OrderProduct::whereStatusCodeIn(OrderStatus::getOpenStatuses())
            ->addSelect([
                'order_products.product_id',
                DB::raw('sum(quantity_to_ship) as total_quantity_to_ship'),
                'inventory.quantity_reserved as quantity_reserved',
            ])
            ->leftJoin('inventory', function ($join) use ($locationId) {
                $join->on('order_products.product_id', '=', 'inventory.product_id');
                $join->on('inventory.location_id', '=', DB::raw($locationId));
            })
            ->groupBy(['order_products.product_id', 'inventory.quantity_reserved'])
            ->limit(500);

        $orderProducts = DB::query()->fromSub($expectedReservedQuantities, 'expected')
            ->whereRaw('total_quantity_to_ship != ifnull(quantity_reserved, 0)')
            ->get();

        foreach ($orderProducts as $record) {
            activity()->on(Product::find($record->product_id))
                ->log('Incorrect quantity on order detected');
            Inventory::query()->updateOrCreate([
                'product_id' => $record->product_id,
                'location_id' => $locationId,
            ], [
                'quantity_reserved' => $record->total_quantity_to_ship
            ]);
        }
    }
}
