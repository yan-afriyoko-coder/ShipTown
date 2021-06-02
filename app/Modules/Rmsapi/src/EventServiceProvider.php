<?php

namespace App\Modules\Rmsapi\src;

use App\Events\SyncRequestedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DisptachSyncJobsListener::class,
        ],
    ];
}
