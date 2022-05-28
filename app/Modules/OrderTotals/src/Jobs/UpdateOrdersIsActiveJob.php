<?php

namespace App\Modules\OrderTotals\src\Jobs;

use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrdersIsActiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $order_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        OrderProduct::query()
            ->selectRaw('
                order_id,
                count(id) as count,
                sum(quantity_ordered) as quantity_ordered,
                sum(quantity_split) as quantity_split,
                sum(quantity_picked) as quantity_picked,
                sum(quantity_skipped_picking) as quantity_skipped_picking,
                sum(quantity_not_picked) as quantity_not_picked,
                sum(quantity_shipped) as quantity_shipped
            ')
            ->where(['order_id' => $this->order_id])
            ->groupBy('order_id')
            ->get()
            ->each(function (OrderProduct $orderProduct) {
                OrderProductTotal::query()->updateOrCreate([
                    'order_id' => $orderProduct['order_id']
                ], [
                    'count' => $orderProduct['count'],
                    'quantity_ordered' => $orderProduct['quantity_ordered'],
                    'quantity_split' => $orderProduct['quantity_split'],
                    'quantity_picked' => $orderProduct['quantity_picked'],
                    'quantity_skipped_picking' => $orderProduct['quantity_skipped_picking'],
                    'quantity_not_picked' => $orderProduct['quantity_not_picked'],
                    'quantity_shipped' => $orderProduct['quantity_shipped'],
                ]);
            });
    }
}
