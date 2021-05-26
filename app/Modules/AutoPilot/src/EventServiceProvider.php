<?php


namespace App\Modules\AutoPilot\src;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\Listeners\ClearPackerIdListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class
        ]
    ];
}
