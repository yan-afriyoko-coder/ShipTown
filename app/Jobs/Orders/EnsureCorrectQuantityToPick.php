<?php

namespace App\Jobs\Orders;

use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnsureCorrectQuantityToPick implements ShouldQueue
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
            ->whereRaw('(quantity_ordered) <> (quantity_to_pick + quantity_picked + quantity_not_picked)')
            ->each(function ($orderProduct) {
                $orderProduct->update(['quantity_to_pick' => \DB::raw('quantity_ordered - quantity_picked - quantity_not_picked ')]);
                activity()->performedOn($orderProduct)->log('Fixed quantity_to_pick');
            });
    }
}
