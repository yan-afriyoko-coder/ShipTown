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

class RunScheduledJobsController extends Controller
{
    public function store(RunScheduledJobsRequest $request)
    {
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
