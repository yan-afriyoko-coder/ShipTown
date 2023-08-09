<?php

namespace App\Jobs;

use App\Events\EveryDayEvent;
use App\Models\Heartbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchEveryDayEventJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
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
        Log::debug('Every Day Event - dispatching');

        EveryDayEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Daily jobs heartbeat missed',
            'expires_at' => now()->addDay()
        ]);

        Log::info('Every Day Event - dispatched successfully');
    }
}
