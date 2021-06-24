<?php

namespace App\Modules\Rmsapi\src;

use App\Events\SyncRequestedEvent;
use App\Modules\ModuleServiceProvider;

class EventServiceProvider extends ModuleServiceProvider
{
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DisptachSyncJobsListener::class,
        ],
    ];
}
