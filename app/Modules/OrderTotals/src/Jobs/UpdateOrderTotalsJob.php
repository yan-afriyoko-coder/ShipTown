<?php

namespace App\Modules\OrderTotals\src\Jobs;

use App\Models\OrderProductTotal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateOrderTotalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Builder $recalculationsTempTable;

    /**
     * @var integer
     */
    private int $order_id;

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
        DB::table('orders_products')
            ->where(['order_id' => $this->order_id])
            ->groupBy('order_id')
            ->selectRaw('
                order_id,
                count(id) as count_expected,
                sum(quantity_ordered) as quantity_ordered_expected,
                sum(quantity_split) as quantity_split_expected,
                sum(quantity_picked) as quantity_picked_expected,
                sum(quantity_skipped_picking) as quantity_skipped_picking_expected,
                sum(quantity_not_picked) as quantity_not_picked_expected,
                sum(quantity_shipped) as quantity_shipped_expected,
                sum(quantity_to_pick) as quantity_to_pick_expected,
                sum(quantity_to_ship) as quantity_to_ship_expected,

                max(updated_at) as updated_at_expected
            ')
            ->get()
            ->each(function ($record) {
                OrderProductTotal::query()
                    ->updateOrCreate([
                        'order_id' => $record->order_id
                    ], [
                        'count' => $record->count_expected,
                        'quantity_ordered' => $record->quantity_ordered_expected,
                        'quantity_split' => $record->quantity_split_expected,
                        'quantity_picked' => $record->quantity_picked_expected,
                        'quantity_skipped_picking' => $record->quantity_skipped_picking_expected,
                        'quantity_not_picked' => $record->quantity_not_picked_expected,
                        'quantity_shipped' => $record->quantity_shipped_expected,
                        'quantity_to_pick' => $record->quantity_to_pick_expected,
                        'quantity_to_ship' => $record->quantity_to_ship_expected,
                        'updated_at' => $record->updated_at_expected,
                    ]);
            });
    }
}
