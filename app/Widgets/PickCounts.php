<?php

namespace App\Widgets;

use App\Models\Picklist;
use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;

class PickCounts extends AbstractWidget
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
        $last7days_count = Picklist::query()
            ->whereDate('picked_at', '>', Carbon::now()->subDays(7))
            ->count();

        return view('widgets.pick_counts', [
            'config' => $this->config,
            'last7days_count' => $last7days_count,
        ]);
    }
}
