<?php

namespace App\Widgets;

use App\Models\Order;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;

class PackedTodayByUser extends AbstractWidget
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
        $startingDate = Carbon::today();

        $count_per_user = Order::query()
            ->select(['packer_user_id', \DB::raw('count(*) as total'), 'users.name'])
            ->whereDate('packed_at', '>=', $startingDate)
            ->leftJoin('users', 'packer_user_id', '=', 'users.id')
            ->groupBy(['packer_user_id'])
            ->orderByDesc('total')
            ->get();

        $total_count = 0;

        foreach ($count_per_user as $count) {
            $total_count += $count['total'];
        }

        return view('widgets.packed_today_by_user', [
            'config' => $this->config,
            'count_per_user' => $count_per_user,
            'total_count' => $total_count,
        ]);
    }
}
