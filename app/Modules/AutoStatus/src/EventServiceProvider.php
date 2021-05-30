<?php

namespace App\Modules\AutoStatus\src;

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
            Listeners\HourlyEvent\RefillStatusPackingWarehouseListener::class,
            Listeners\HourlyEvent\RefillStatusSingleLineOrdersListener::class,
            Listeners\HourlyEvent\RefillStatusesListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\UpdateClosedAt::class,
            Listeners\OrderUpdatedEvent\ProcessingToPaidListener::class,
            Listeners\OrderUpdatedEvent\SetReadyWhenPacked::class,
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
