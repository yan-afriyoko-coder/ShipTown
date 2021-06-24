<?php


namespace App\Modules\AutoPilot\src;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\Listeners\ClearPackerIdListener;
use App\Modules\ModuleServiceProvider;

class EventServiceProvider extends ModuleServiceProvider
{
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class
        ]
    ];
}
