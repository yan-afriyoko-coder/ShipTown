<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Events\Every10minEvent;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class Every10minEventListener
{
    public function handle(Every10minEvent $event)
    {
        Log::debug('heartbeat', ['code' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Daily event heartbeat missed',
            'expires_at' => now()->addMinutes(30)
        ]);
    }
}
