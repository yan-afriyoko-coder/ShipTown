<?php

namespace App\Widgets;

use App\Models\Order;

class Apt extends BaseWidget
{
    protected $name = 'apt';

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $statuses = [
        'complete',
        'complete_manually_processed',
        'completed_imported_to_rms'
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $apt_seconds  = (integer) Order::query()
            ->selectRaw("AVG(TIME_TO_SEC(TIMEDIFF(order_closed_at, order_placed_at))) as apt")
            ->whereIn('status_code', $this->statuses)
            ->whereRaw('order_closed_at > order_placed_at')
            ->whereRaw('order_closed_at BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() ')
            ->value('apt');

        return view('widgets.apt', [
            'config' => $this->config,
            'apt_string' => $this->timeDiffForPrez($apt_seconds),
            'statuses' => $this->statuses,
            'widget_id' => $this->widgetId,
            'widget_name' => $this->name
        ]);
    }

    function timeDiffForPrez(int $diffInSeconds)
    {
        $result = '';

        $data = array(
            'd' => 86400,
            'h' => 3600,
            'm' => 60
        );

        foreach ($data as $k => $v) {
            if ($diffInSeconds >= $v) {
                $diff = floor($diffInSeconds / $v);
                $result .= " $diff" . ($diff > 1 ? $k : substr($k, 0, -1));
                $diffInSeconds -= $v * $diff;
            }
        }

        return $result;
    }
}
