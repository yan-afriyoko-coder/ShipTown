<?php

namespace App\Jobs;

use App\Events\EveryHourEvent;
use App\Models\Heartbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RunHourlyListener.
 */
class DispatchEveryHourEventJobs implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 120;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this)]);
    }

    public function handle()
    {
        Log::debug('Every Hour Event - dispatching');

        EveryHourEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Hour Event heartbeat missed',
            'expires_at' => now()->addHours(2)
        ]);

        Log::info('Every Hour event - dispatched successfully');
    }
}
