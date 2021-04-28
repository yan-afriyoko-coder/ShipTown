<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateQuantityReservedJob implements ShouldQueue
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
        $checkedProductIds = [];

        $reservingStatusCodes = OrderStatus::whereReservesStock(true)
            ->select(['code'])
            ->get();

        OrderProduct::whereStatusCodeIn($reservingStatusCodes)
            ->select([
                'product_id',
                DB::raw('sum(quantity_to_ship) as new_quantity_reserved')
            ])
            ->groupBy('product_id')
            ->get()
            ->each(function (OrderProduct $orderProduct) use (&$checkedProductIds) {
                $inventory = $orderProduct->product->inventory()
                    ->where('location_id', '=', 999)
                    ->first();

                $checkedProductIds[] = $orderProduct->product_id;

                if ($inventory->quantity_reserved != $orderProduct->getAttribute('new_quantity_reserved')) {
                    $inventory->quantity_reserved = $orderProduct->getAttribute('new_quantity_reserved');
                    $inventory->save();
                    $orderProduct->product->log('Recalculated quantity_reserved');
                }
            });

            Inventory::whereLocationId(999)
                ->where('quantity_reserved', '!=', 0)
                ->whereNotIn('product_id', $checkedProductIds)
                ->get()
                ->each(function (Inventory $inventory) {
                    $inventory->product->log('Resetting quantity reserved');
                    $inventory->quantity_reserved = 0;
                    $inventory->save();
                });
    }
}
