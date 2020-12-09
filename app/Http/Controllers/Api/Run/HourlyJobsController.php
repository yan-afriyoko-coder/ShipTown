<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunMaintenanceJobs;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class HourlyJobsController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        RunMaintenanceJobs::dispatch();
        return 'Jobs Dispatched';
    }
}
