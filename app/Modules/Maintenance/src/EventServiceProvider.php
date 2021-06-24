<?php


namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use App\Modules\ModuleServiceProvider;

class EventServiceProvider extends ModuleServiceProvider
{
    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEvent\RunDailyMaintenanceJobsListener::class,
        ],
    ];
}
