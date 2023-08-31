<?php

namespace App\Modules\OrderTotals\src\Services;

use App\Models\OrderProductTotal;
use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;
use Illuminate\Support\Facades\DB;

class OrderTotalsService
{
    public static function dispatchDailyJobs(): void
    {
        EnsureAllRecordsExistsJob::dispatch();
        EnsureCorrectTotalsJob::dispatch();
    }

    public static function updateTotals(int $order_id)
    {
        $record = DB::table('orders_products')
            ->where(['order_id' => $order_id])
            ->whereNull('deleted_at')
            ->groupBy('order_id')
            ->selectRaw('
                order_id,
                count(id) as count_expected,
                sum(quantity_ordered) as quantity_ordered_expected,
                sum(quantity_split) as quantity_split_expected,
                sum(total_price) as total_price_expected,
                sum(quantity_picked) as quantity_picked_expected,
                sum(quantity_skipped_picking) as quantity_skipped_picking_expected,
                sum(quantity_not_picked) as quantity_not_picked_expected,
                sum(quantity_shipped) as quantity_shipped_expected,
                sum(quantity_to_pick) as quantity_to_pick_expected,
                sum(quantity_to_ship) as quantity_to_ship_expected,
                max(updated_at) as max_updated_at_expected
            ')
            ->get()
            ->first();

        $data = $record === null ? [] : [
            'count' => $record->count_expected,
            'quantity_ordered' => $record->quantity_ordered_expected,
            'quantity_split' => $record->quantity_split_expected,
            'total_price' => $record->total_price_expected,
            'quantity_picked' => $record->quantity_picked_expected,
            'quantity_skipped_picking' => $record->quantity_skipped_picking_expected,
            'quantity_not_picked' => $record->quantity_not_picked_expected,
            'quantity_shipped' => $record->quantity_shipped_expected,
            'quantity_to_pick' => $record->quantity_to_pick_expected,
            'quantity_to_ship' => $record->quantity_to_ship_expected,
            'max_updated_at' => $record->max_updated_at_expected
        ];

        OrderProductTotal::query()->updateOrCreate(['order_id' => $order_id], $data);
    }
}
