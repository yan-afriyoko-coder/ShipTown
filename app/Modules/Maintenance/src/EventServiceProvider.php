<?php


namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use App\Modules\Maintenance\src\Listeners\DailyEvent\RunFixQuantityAvailableJobListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DailyEvent::class => [
            RunFixQuantityAvailableJobListener::class,
        ]
    ];
}
