<?php

namespace App\Modules\Rmsapi\src;

use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

class EventServiceProviderBase extends BaseModuleServiceProvider
{
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DisptachSyncJobsListener::class,
        ],
    ];
}
