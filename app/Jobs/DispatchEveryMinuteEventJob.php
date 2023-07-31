<?php

namespace App\Jobs;

use App\Events\EveryMinuteEvent;
use App\Models\Heartbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchEveryMinuteEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('DispatchEvery10minEvent - dispatching');

        EveryMinuteEvent::dispatch();

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Minute heartbeat missed',
            'expires_at' => now()->addHour(),
        ]);

        Log::info('DispatchEvery10minEvent - dispatched successfully');
    }
}
