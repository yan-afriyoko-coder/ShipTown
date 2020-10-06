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
        $status_order_counts = Order::query()
            ->select(['status_code', DB::raw('count(*) as order_count')])
            ->whereIn('status_code', OrderStatus::getCompletedStatusCodeList())
            ->whereDate('order_closed_at', '>', Carbon::now()->subDays(7))
            ->groupBy(['status_code'])
            ->get();

        $total_count = $status_order_counts->sum(function ($day) {
            return $day['order_count'];
        });

        return view('widgets.completed_orders_counts', [
            'config' => $this->config,
            'status_order_counts' => $status_order_counts,
            'total_count' => $total_count,
        ]);
    }
}
