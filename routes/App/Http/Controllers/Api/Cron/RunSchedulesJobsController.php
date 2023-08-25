<?php

namespace App\Http\Controllers\Api\Cron;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunScheduledJobsRequest;
use App\Jobs\SyncRequestJob;

class RunSchedulesJobsController extends Controller
{
    public function store(RunScheduledJobsRequest $request)
    {
        SyncRequestJob::dispatch();

        info('SyncRequestJob dispatched');

        $this->respondOK200('Sync requested successfully');
    }
}
