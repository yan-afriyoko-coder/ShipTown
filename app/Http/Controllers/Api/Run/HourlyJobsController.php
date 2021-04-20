<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunHourlyJobs;
use Illuminate\Support\Facades\Request;

/**
 * Class HourlyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class HourlyJobsController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        RunHourlyJobs::dispatch();
        $this->respondOK200('Hourly jobs Dispatched');
    }
}
