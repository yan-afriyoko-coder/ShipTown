<?php

namespace App\Providers;

use App\Listeners\Order\Created\PublishSnsNotificationListener;
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
            PublishSnsNotificationListener::class
        ],

        \App\Events\Order\OrderUpdatedEvent::class => [
            \App\Listeners\Order\Updated\PublishSnsNotificationListener::class,
            \App\Listeners\Order\Updated\CheckAndMarkPaidListener::class,
            \App\Listeners\Order\Updated\ChangeToPackingWebStatusListener::class,
            \App\Listeners\Order\Updated\ChangeStatusToReadyIfPackedListener::class,
        ],

        \App\Events\Order\StatusChangedEvent::class => [
            \App\Listeners\Order\StatusChanged\UpdateClosedAt::class,
        ],

        // Inventory
        \App\Events\Inventory\InventoryUpdatedEvent::class => [
            \App\Listeners\Inventory\InventoryUpdatedEventListener::class
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
