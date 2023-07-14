<?php

namespace App\Jobs;

use App\Events\EveryHourEvent;
use App\Events\HourlyEvent;
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
class RunHourlyJobs implements ShouldQueue
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
        Log::debug('Hourly event - dispatching');

        HourlyEvent::dispatch();

        EveryHourEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Hourly jobs heartbeat missed',
            'expires_at' => now()->addHours(2)
        ]);

        Log::info('Hourly event - dispatched successfully');
    }
}
