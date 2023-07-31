<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class HourlyEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['event' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Hour Event heartbeat missed',
            'expires_at' => now()->addHours(2)
        ]);
    }
}
