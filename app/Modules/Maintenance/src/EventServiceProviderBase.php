<?php


namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use App\Modules\BaseModuleServiceProvider;

class EventServiceProviderBase extends BaseModuleServiceProvider
{
    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEvent\RunDailyMaintenanceJobsListener::class,
        ],
    ];
}
