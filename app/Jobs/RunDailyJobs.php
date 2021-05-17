<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunDailyJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array|string[]
     */
    private array $jobClassesToRun = [
        \App\Modules\Api2cart\src\Jobs\ResyncLastDayJob::class,
        \App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob::class,
        \App\Modules\Api2cart\src\Jobs\ResyncSyncErrorsTaggedJob::class,
        \App\Modules\Api2cart\src\Jobs\ResyncCheckFailedTaggedJob::class,
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = collect($this->jobClassesToRun)
            ->map(function ($jobClass) {
                return new $jobClass;
            })->all();

        $this->chain($jobs);
    }
}
