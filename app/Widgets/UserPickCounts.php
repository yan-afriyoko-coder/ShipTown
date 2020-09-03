<?php

namespace App\Widgets;

use App\Models\Pick;
use App\Models\Picklist;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;

class UserPickCounts extends AbstractWidget
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
        $startingDate = Carbon::now()->subDays(7);

        $count_per_user = Pick::query()
            ->select('picker_user_id', \DB::raw('count(*) as total'), 'users.name')
            ->whereDate('picked_at', '>', $startingDate)
            ->leftJoin('users', 'picker_user_id', '=', 'users.id')
            ->groupBy(['picker_user_id'])
            ->get();

        $total_count = 0;

        foreach ($count_per_user as $count) {
            $total_count += $count['total'];
        }

        return view('widgets.user_pick_counts', [
            'config' => $this->config,
            'count_per_user' => $count_per_user,
            'total_count' => $total_count,
        ]);
    }
}
