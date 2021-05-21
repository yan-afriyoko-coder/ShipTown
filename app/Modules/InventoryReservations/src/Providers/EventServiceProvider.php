<?php

namespace App\Modules\InventoryReservations\src\Providers;

use App\Events\Inventory\InventoryCreatedEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\InventoryReservations\src\Listeners\InventoryCreatedListener;
use App\Modules\InventoryReservations\src\Listeners\InventoryUpdatedListener;
use App\Modules\InventoryReservations\src\Listeners\OrderProductCreatedListener;
use App\Modules\InventoryReservations\src\Listeners\OrderUpdatedListener;
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
        OrderUpdatedEvent::class => [
            OrderUpdatedListener::class,
        ],

        OrderProductCreatedEvent::class => [
            OrderProductCreatedListener::class,
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
