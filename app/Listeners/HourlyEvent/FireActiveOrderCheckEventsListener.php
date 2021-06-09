<?php

namespace App\Listeners\HourlyEvent;

use App\Jobs\FireActiveOrderCheckEventsJob;

class FireActiveOrderCheckEventsListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        FireActiveOrderCheckEventsJob::dispatch();
    }
}
