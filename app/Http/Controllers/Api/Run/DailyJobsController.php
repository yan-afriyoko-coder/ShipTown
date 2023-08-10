<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchEveryDayEventJob;

/**
 * Class DailyJobsController.
 */
class DailyJobsController extends Controller
{
    public function index()
    {
        DispatchEveryDayEventJob::dispatch();

        $this->respondOK200('Daily jobs dispatched');
    }
}
