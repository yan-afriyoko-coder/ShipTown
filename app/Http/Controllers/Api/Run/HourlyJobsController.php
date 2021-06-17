<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunHourlyJobs;

/**
 * Class HourlyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class HourlyJobsController extends Controller
{
    /**
     *
     */
    public function index()
    {
        RunHourlyJobs::dispatch();

        $this->respondOK200('Hourly jobs Dispatched');
    }
}
