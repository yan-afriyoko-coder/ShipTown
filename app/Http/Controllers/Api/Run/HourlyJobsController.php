<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchEveryHourEventJobs;

/**
 * Class HourlyJobsController.
 */
class HourlyJobsController extends Controller
{
    public function index()
    {
        DispatchEveryHourEventJobs::dispatch();

        $this->respondOK200('Hourly jobs Dispatched');
    }
}
