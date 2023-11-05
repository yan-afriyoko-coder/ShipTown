<?php

namespace App\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\MonthlyEvent;
use App\Models\Heartbeat;

class DispatchMonthlyEventJob extends UniqueJob
{
    public function handle()
    {
        MonthlyEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Monthly event heartbeat missed',
            'expires_at' => now()->addMonth()->addDay(),
        ]);
    }
}
