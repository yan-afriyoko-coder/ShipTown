<?php

namespace App\Widgets;

use App\Models\Pick;
use Arrilot\Widgets\AbstractWidget;

class PicksReport extends AbstractWidget
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
        $picks = Pick::getSpatieQueryBuilder()
            ->with('user')
            ->latest()
            ->limit(1000)
            ->get();

        return view('widgets.picks_report', [
            'config' => $this->config,
            'picks'  => $picks->toArray(),
        ]);
    }
}
