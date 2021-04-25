<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        // for each reserved OrderProduct
        $statusCodes = OrderStatus::whereReservesStock(true)->select(['code'])->get();

        $orderProducts = OrderProduct::whereStatusCodeIn($statusCodes)
            ->select('product_id')
            ->distinct()
            ->get();

//        $orderProducts->each(function (OrderProduct $orderProduct) {
//            $orderProduct->quantity_to_ship;
//        });
    }
}
