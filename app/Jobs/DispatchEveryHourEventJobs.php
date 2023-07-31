<?php

namespace App\Jobs;

use App\Events\EveryHourEvent;
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
class DispatchEveryHourEventJobs implements ShouldQueue
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
        Log::debug('Every Hourly event - dispatching');

        EveryHourEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Hour Jobs heartbeat missed',
            'expires_at' => now()->addHours(2)
        ]);

        Log::info('Every Hour event - dispatched successfully');
    }
}
