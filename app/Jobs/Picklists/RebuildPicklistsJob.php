<?php

namespace App\Jobs\Picklists;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RebuildPicklistsJob implements ShouldQueue
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
        // delete all picklists
        Picklist::query()->delete();

        $activeOrders = collect(
            Order::active()
                ->orderBy('order_placed_at')
                ->get()
        );

        $activeOrders->each(function ($order) {

            $orderProductsCollection = collect(
                $order->orderProducts()
                    ->get()
                    ->toArray()
            );

            $orderProductsCollection->each(function ($orderProduct) {
                Picklist::query()->create([
                    'product_id' =>         $orderProduct['product_id'],
                    'location_id' =>        'WWW',
                    'sku_ordered' =>        $orderProduct['sku_ordered'],
                    'name_ordered' =>       $orderProduct['name_ordered'],
                    'quantity_to_pick' =>   $orderProduct['quantity'],
                ]);
            });

        });


    }
}
