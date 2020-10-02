<?php

namespace App\Widgets;

use App\Models\Order;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NewOrdersCounts extends AbstractWidget
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
//        $orderStatusesToExclude = array_merge(
//            OrderStatus::getActiveStatusCodesList(),
//            OrderStatus::getToFollowStatusList()
//        );

        $orders_counts = Order::query()
            ->whereDate('order_placed_at', '>', Carbon::now()->subDays(7))
//            ->whereNotIn('status_code', $orderStatusesToExclude)
            ->groupBy(['status_code'])
            ->select(['status_code', DB::raw('count(*) as order_count')])
            ->get();

        $total_count = 0;

        foreach ($orders_counts as $order_status) {
            $total_count += $order_status['order_count'];
        }

        return view('widgets.new_orders_counts', [
            'config' => $this->config,
            'new_order_counts' => $orders_counts,
            'total_count' => $total_count,
        ]);
    }
}
