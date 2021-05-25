<?php

namespace App\Modules\InventoryReservations\src;

use App\Events\DailyEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\InventoryReservations\src\Listeners\DailyEvent\RunRecalculateQuantityReservedJobListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DailyEvent::class => [
            RunRecalculateQuantityReservedJobListener::class
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],

        OrderProductCreatedEvent::class => [
            Listeners\OrderProductCreatedListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
