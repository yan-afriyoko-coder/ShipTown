<?php

namespace App\Jobs\OrderProducts;

use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateQuantityToPickJob implements ShouldQueue
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
            ->whereRaw('(quantity_to_pick) <> (quantity_ordered - quantity_picked - quantity_skipped_picking)')
            ->latest('updated_at')
            ->limit(500);

        $records->each(function (OrderProduct $orderProduct) {
                $orderProduct->log('Incorrect quantity to pick detected')
                    // quantity_to_pick is recalculated on model save
                    ->save();
        });

        info('RecalculateQuantityToPickJob finished', ['record_recalculated' => $records->count()]);
    }
}
