<?php

namespace App\Jobs;

use App\Events\Every10minEvent;
use App\Events\EveryTenMinutesEvent;
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
class DispatchEveryTenMinutesEventJob implements ShouldQueue
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

        EveryTenMinutesEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every 10 Minutes heartbeat missed',
            'expires_at' => now()->addHour()
        ]);

        Log::info('DispatchEvery10minEvent - dispatched successfully');
    }
}
