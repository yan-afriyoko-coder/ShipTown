<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Jobs\DispatchEveryMinuteEventJob;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryMonthEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['code' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Month heartbeat missed',
            'expires_at' => now()->addMonth()->addDay(),
            'auto_heal_job_class' => DispatchEveryMinuteEventJob::class
        ]);
    }
}
