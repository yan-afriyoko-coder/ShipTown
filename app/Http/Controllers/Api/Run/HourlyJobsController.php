<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunMaintenanceJobs;
use Illuminate\Support\Facades\Request;

/**
 * Class HourlyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class HourlyJobsController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        RunMaintenanceJobs::dispatch();
        return 'Jobs Dispatched';
    }
}
