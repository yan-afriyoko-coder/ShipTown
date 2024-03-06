<?php

namespace App\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\EveryMonthEvent;
use App\Models\Heartbeat;

class DispatchMonthlyEventJob extends UniqueJob
{
    public function handle()
    {
        EveryMonthEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Monthly event heartbeat missed',
            'expires_at' => now()->addMonth()->addDay(),
        ]);
    }
}
