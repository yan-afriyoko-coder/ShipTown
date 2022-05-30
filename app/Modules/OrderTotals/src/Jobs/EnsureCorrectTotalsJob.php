<?php

namespace App\Modules\OrderTotals\src\Jobs;

use App\Helpers\TemporaryTable;
use App\Models\OrderProductTotal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class EnsureCorrectTotalsJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        IsMonitored;

    private Builder $recalculationsTempTable;

    private string $recalculations_temp_table_name;

    public function __construct()
    {
        $this->recalculations_temp_table_name = 'recalculations_' . rand(10000000, 99999999);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('starting');
        $this->prepareTempTable();

        $this->fixMissingOrWrongTotals();
    }

    public function fixMissingOrWrongTotals()
    {
//        do {
            $records = $this->missingOrWrongTotalsQuery()
                ->limit(10000)
                ->get();

            $records->each(function ($record) {
                OrderProductTotal::query()
                    ->where(['order_id' => $record->order_id])
                    ->update([
                        'count' => $record->count_expected ?? 0,
                        'quantity_ordered' => $record->quantity_ordered_expected ?? 0,
                        'quantity_split' => $record->quantity_split_expected ?? 0,
                        'quantity_picked' => $record->quantity_picked_expected ?? 0,
                        'quantity_skipped_picking' => $record->quantity_skipped_picking_expected ?? 0,
                        'quantity_not_picked' => $record->quantity_not_picked_expected ?? 0,
                        'quantity_shipped' => $record->quantity_shipped_expected ?? 0,
                        'quantity_to_pick' => $record->quantity_to_pick_expected ?? 0,
                        'quantity_to_ship' => $record->quantity_to_ship_expected ?? 0,
                        'max_updated_at' => $record->max_updated_at_expected ?? 0,
                    ]);

                    Log::warning('EnsureCorrectTotalsJob - order updated', ['order_id' => $record->order_id]);
            });
//        } while ($records->isNotEmpty());
    }

    private function prepareTempTable()
    {
        $subQuery = DB::table('orders_products')
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

                        max(updated_at) as max_updated_at_expected
                    ')
            ->groupBy('order_id');

        TemporaryTable::create($this->recalculations_temp_table_name, $subQuery);
    }

    /**
     * @return Builder
     */
    private function missingOrWrongTotalsQuery(): Builder
    {
        return DB::table('orders_products_totals')
            ->selectRaw('
                orders_products_totals.order_id,

                orders_products_totals.count,
                orders_products_totals.quantity_ordered,
                orders_products_totals.quantity_split,
                orders_products_totals.quantity_picked,
                orders_products_totals.quantity_skipped_picking,
                orders_products_totals.quantity_not_picked,
                orders_products_totals.quantity_shipped,
                orders_products_totals.quantity_to_pick,
                orders_products_totals.quantity_to_ship,
                orders_products_totals.max_updated_at,

                recalculations.count_expected,
                recalculations.quantity_ordered_expected,
                recalculations.quantity_split_expected,
                recalculations.quantity_picked_expected,
                recalculations.quantity_skipped_picking_expected,
                recalculations.quantity_not_picked_expected,
                recalculations.quantity_shipped_expected,
                recalculations.quantity_to_pick_expected,
                recalculations.quantity_to_ship_expected,
                recalculations.max_updated_at_expected
            ')
            ->leftJoin(
                $this->recalculations_temp_table_name .' as recalculations',
                'recalculations.order_id',
                '=',
                'orders_products_totals.order_id'
            )
            ->whereRaw('
              (
                   (orders_products_totals.count                    != recalculations.count_expected                   )
                OR (orders_products_totals.quantity_ordered         != recalculations.quantity_ordered_expected        )
                OR (orders_products_totals.quantity_split           != recalculations.quantity_split_expected          )
                OR (orders_products_totals.quantity_picked          != recalculations.quantity_picked_expected         )
                OR (orders_products_totals.quantity_skipped_picking != recalculations.quantity_skipped_picking_expected)
                OR (orders_products_totals.quantity_not_picked      != recalculations.quantity_not_picked_expected     )
                OR (orders_products_totals.quantity_shipped         != recalculations.quantity_shipped_expected        )
                OR (orders_products_totals.quantity_to_pick         != recalculations.quantity_to_pick_expected        )
                OR (orders_products_totals.quantity_to_ship         != recalculations.quantity_to_ship_expected        )
                OR (orders_products_totals.max_updated_at           != recalculations.max_updated_at_expected          )
              )
            ');
    }
}
