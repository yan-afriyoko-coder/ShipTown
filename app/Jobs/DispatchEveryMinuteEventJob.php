<?php

namespace App\Jobs;

use App\Events\EveryMinuteEvent;
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

        Log::info('DispatchEvery10minEvent - dispatched successfully');
    }
}
