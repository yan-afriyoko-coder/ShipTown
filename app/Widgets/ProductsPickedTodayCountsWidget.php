<?php

namespace App\Widgets;

use App\Models\Pick;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;

class ProductsPickedTodayCountsWidget extends AbstractWidget
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

        $count_per_user = Pick::query()
            ->select(['user_id', \DB::raw('count(*) as total'), 'users.name'])
            ->whereDate('picks.created_at', '>=', $startingDate)
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->groupBy(['user_id'])
            ->get();

        $total_count = 0;

        foreach ($count_per_user as $count) {
            $total_count += $count['total'];
        }

        return view('widgets.products_picked_today_counts_widget', [
            'config' => $this->config,
            'count_per_user' => $count_per_user,
            'total_count' => $total_count,
        ]);
    }
}
