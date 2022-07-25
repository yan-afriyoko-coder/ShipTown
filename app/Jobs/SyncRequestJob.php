<?php

namespace App\Jobs;

use App\Events\Every10minEvent;
use App\Events\SyncRequestedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RunHourlyListener.
 */
class SyncRequestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SyncRequestedEvent::dispatch();

        Log::info('SyncRequestedEvent dispatched');

        Every10minEvent::dispatch();

        Log::info('Every10minEvent dispatched');
    }
}
