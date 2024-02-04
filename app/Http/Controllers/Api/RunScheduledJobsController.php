<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunScheduledJobsRequest;
use App\Jobs\DispatchEveryDayEventJob;
use App\Jobs\DispatchEveryFiveMinutesEventJob;
use App\Jobs\DispatchEveryHourEventJobs;
use App\Jobs\DispatchEveryMinuteEventJob;
use App\Jobs\DispatchEveryTenMinutesEventJob;
use App\Jobs\DispatchMonthlyEventJob;
use App\Jobs\DispatchWeeklyEventJob;
use App\Jobs\SyncRequestJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;

class RunScheduledJobsController extends Controller
{
    public function store(RunScheduledJobsRequest $request)
    {
        $runnableJobs = collect([
            'MODULE_RMSAPI_ProcessImportedProductRecordsJob' => ProcessImportedProductRecordsJob::class,
        ]);

        if ($runnableJobs->has($request->validated('job'))) {
            $job = new $runnableJobs[$request->validated('job')];
            $job->dispatch();

            return response()->json([
                'message' => 'Job dispatched successfully',
                'job' => $request->validated('job')
            ]);
        }


        switch ($request->validated('schedule')) {
            case 'EveryMinute':
                DispatchEveryMinuteEventJob::dispatch();
                break;
            case 'EveryFiveMinutes':
                DispatchEveryFiveMinutesEventJob::dispatch();
                break;
            case 'EveryTenMinutes':
                DispatchEveryTenMinutesEventJob::dispatch();
                break;
            case 'EveryHour':
                DispatchEveryHourEventJobs::dispatch();
                break;
            case 'EveryDay':
                DispatchEveryDayEventJob::dispatch();
                break;
            case 'EveryWeek':
                DispatchWeeklyEventJob::dispatch();
                break;
            case 'EveryMonth':
                DispatchMonthlyEventJob::dispatch();
                break;
            case 'SyncRequest':
                SyncRequestJob::dispatch();
                break;
            default:
                $this->respondBadRequest('Invalid schedule');
        }

        $this->respondOK200('Sync requested successfully');
    }
}
