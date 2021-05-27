<?php


namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEvent\RunDailyMaintenanceJobsListener::class,
        ],
    ];
}
