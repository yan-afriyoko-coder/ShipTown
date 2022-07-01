<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Events\HourlyEvent;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class HourlyEventListener
{
    public function handle(HourlyEvent $event)
    {
        Log::debug('heartbeat', ['event' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Hourly event heartbeat missed',
            'expires_at' => now()->addMinutes(30)
        ]);
    }
}
