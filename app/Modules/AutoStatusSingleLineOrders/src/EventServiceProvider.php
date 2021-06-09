<?php

namespace App\Modules\AutoStatusSingleLineOrders\src;

use App\Events\HourlyEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEvent\RefillStatusSingleLineOrdersListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
