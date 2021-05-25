<?php

namespace App\Jobs;

use App\Events\DailyEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunDailyJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array|string[]
     */
    private array $jobClassesToRun = [
        \App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob::class,
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->jobClassesToRun)->each(function ($jobClass) {
            dispatch(new $jobClass);
        });

        DailyEvent::dispatch();

        Log::info('Daily jobs dispatched');
    }
}
