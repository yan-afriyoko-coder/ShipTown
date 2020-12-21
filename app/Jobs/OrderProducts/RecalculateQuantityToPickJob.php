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
        OrderProduct::query()
            ->whereRaw('(quantity_to_pick) <> (quantity_ordered - quantity_picked - quantity_skipped_picking)')
            ->latest('updated_at')
            ->limit(500)
            ->each(function (OrderProduct $orderProduct) {
                $orderProduct
                    ->log('Incorrect quantity to pick detected')
                    ->update([
                        'quantity_to_pick' => \DB::raw('quantity_ordered - quantity_picked - quantity_skipped_picking')
                    ]);
            });
    }
}
