<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
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

        // App
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Product
        \App\Events\Product\ProductCreatedEvent::class => [
            \App\Listeners\Product\ProductCreatedEventListener::class,
        ],

        \App\Events\Product\ProductUpdatedEvent::class => [
            \App\Listeners\Product\ProductUpdatedEventListener::class,
        ],

        // Order
        \App\Events\Order\OrderCreatedEvent::class => [
            \App\Listeners\Order\OrderCreatedEventListener::class
        ],

        \App\Events\Order\OrderUpdatedEvent::class => [
            \App\Listeners\Order\OrderUpdatedEventListener::class,
            \App\Modules\InventoryReservations\src\Listeners\OrderUpdatedListener::class,
        ],

        // OrderProduct
        \App\Events\OrderProduct\OrderProductCreatedEvent::class => [
            \App\Modules\InventoryReservations\src\Listeners\OrderProductCreatedListener::class,
        ],

        // Inventory
        \App\Events\Inventory\InventoryCreatedEvent::class => [
            \App\Listeners\Inventory\InventoryCreatedEventListener::class,
            \App\Modules\InventoryReservations\src\Listeners\InventoryCreatedListener::class,

        ],

        \App\Events\Inventory\InventoryUpdatedEvent::class => [
            \App\Listeners\Inventory\InventoryUpdatedEventListener::class,
            \App\Modules\InventoryReservations\src\Listeners\InventoryUpdatedListener::class,
        ],

        // Other
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

        //
    }
}
