<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchEveryDayEvenJob;

/**
 * Class DailyJobsController.
 */
class DailyJobsController extends Controller
{
    public function index()
    {
        DispatchEveryDayEvenJob::dispatch();

        $this->respondOK200('Daily jobs dispatched');
    }
}
