<?php

namespace App\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\WeeklyEvent;
use App\Models\Heartbeat;

class DispatchWeeklyEventJob extends UniqueJob
{
    public function handle()
    {
        WeeklyEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Weekly jobs heartbeat missed',
            'expires_at' => now()->addWeek()
        ]);
    }
}
