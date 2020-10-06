<?php

namespace App\Widgets;

use App\Models\Order;
use App\Models\OrderStatus;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompletedStatusOrderCount extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $orderStatusesToExclude = array_merge(
            OrderStatus::getActiveStatusCodesList(),
            OrderStatus::getToFollowStatusList()
        );

        $status_order_counts = Order::query()
            ->whereDate('order_placed_at', '>', Carbon::now()->subDays(7))
            ->whereNotIn('status_code', $orderStatusesToExclude)
            ->groupBy(['status_code'])
            ->select(['status_code', DB::raw('count(*) as order_count')])
            ->get();

        return view('widgets.completed_orders_counts', [
            'config' => $this->config,
            'status_order_counts' => $status_order_counts
        ]);
    }
}
