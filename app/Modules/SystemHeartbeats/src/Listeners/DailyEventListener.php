<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Events\DailyEvent;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class DailyEventListener
{
    public function handle(DailyEvent $event)
    {
        Log::debug('heartbeat', ['event' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Daily event heartbeat missed',
            'expires_at' => now()->addDays(2)
        ]);
    }
}
