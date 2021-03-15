<?php

namespace App\Jobs\OrderProducts;

use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $records = OrderProduct::query()
            ->whereRaw('quantity_to_ship != quantity_ordered - quantity_shipped')
            ->latest()
            // for performance purposes
            ->limit(1000);

        $records->each(function (OrderProduct $orderProduct) {
                $orderProduct->log('Incorrect quantity to ship detected')
                    // quantity_to_ship is recalculated on model save
                    ->save();
        });

        info('RecalculateQuantityToShipJob finished', ['record_recalculated' => $records->count()]);
    }
}
