<?php

namespace App\Widgets;

use App\Modules\Reports\src\Models\Report;
use Arrilot\Widgets\AbstractWidget;

class ReportWidget extends AbstractWidget
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
        /** @var Report $report */
        $report = app($this->config['report']);

        $report->view = data_get($this->config, 'view', $report->view);

        return $report->toView();
    }
}
