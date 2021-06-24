<?php


namespace App\Modules\AutoPilot\src;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\Listeners\ClearPackerIdListener;
use App\Modules\BaseModuleServiceProvider;

class EventServiceProviderBase extends BaseModuleServiceProvider
{
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class
        ]
    ];
}
