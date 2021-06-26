<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunDailyJobs;

/**
 * Class DailyJobsController.
 */
class DailyJobsController extends Controller
{
    public function index()
    {
        RunDailyJobs::dispatch();

        $this->respondOK200('Daily jobs dispatched');
    }
}
