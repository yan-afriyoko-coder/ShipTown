<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunDailyJobs;
use Illuminate\Http\Request;

/**
 * Class DailyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class DailyJobsController extends Controller
{
    /**
     */
    public function index()
    {
        RunDailyJobs::dispatch();

        $this->respondOK200('Daily jobs dispatched');
    }
}
