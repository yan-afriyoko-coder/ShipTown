<?php

namespace App\Jobs;

use App\Events\Every10minEvent;
use App\Events\SyncRequestedEvent;
use App\Models\Heartbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RunHourlyListener.
 */
class DispatchEvery10minEventJob implements ShouldQueue
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
        Log::debug('DispatchEvery10minEvent - dispatching');

        Every10minEvent::dispatch();

        SyncRequestedEvent::dispatch();

        Log::info('DispatchEvery10minEvent - dispatched successfully');
    }
}
