<?php

namespace App\Jobs\Maintenance;

use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RecalculateQuantityToShipJob implements ShouldQueue
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
        OrderProduct::whereStatusCodeIn(OrderStatus::getOpenStatuses())
            ->whereRaw('quantity_to_ship != quantity_ordered - quantity_shipped')
            ->latest()
            // for performance purposes limit to 1000 records per job
            ->limit(1000)
            ->each(function ($orderProduct) {
                activity()->on($orderProduct)->log('Incorrect quantity to ship detected');
                $orderProduct->save();
            });
    }
}
