<?php

namespace App\Jobs;

use App\Events\EveryFiveMinutesEvent;
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
class DispatchEveryFiveMinutesEventJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
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
        Log::debug('DispatchEveryFiveMinutesEvent - dispatching');

        EveryFiveMinutesEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every 5 Minutes heartbeat missed',
            'expires_at' => now()->addHour()
        ]);

        Log::info('DispatchEveryFiveMinutesEvent - dispatched successfully');
    }
}
