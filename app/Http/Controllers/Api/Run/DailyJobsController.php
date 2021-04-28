<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\RunDailyJobs;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class DailyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class DailyJobsController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        RunDailyJobs::dispatch();

        $this->respondOK200('Daily jobs dispatched');
    }
}
