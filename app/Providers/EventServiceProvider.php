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
            \App\Modules\Sns\src\Listeners\ProductCreatedEvent\PublishSnsNotificationListener::class,
        ],

        \App\Events\Product\ProductUpdatedEvent::class => [
            \App\Modules\Sns\src\Listeners\ProductUpdatedEvent\PublishSnsNotificationListener::class,
        ],

        // Order
        \App\Events\Order\OrderCreatedEvent::class => [
            \App\Listeners\Order\OrderCreatedEventListener::class,
            \App\Modules\Sns\src\Listeners\OrderCreatedEvent\PublishSnsNotificationListener::class,
        ],

        \App\Events\Order\OrderUpdatedEvent::class => [
            \App\Listeners\Order\OrderUpdatedEventListener::class,
            \App\Modules\Sns\src\Listeners\OrderUpdatedEvent\PublishSnsNotificationListener::class,
        ],

        \App\Events\Inventory\InventoryUpdatedEvent::class => [
            \App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\AttachOutOfStockTagListener::class,
            \App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\DetachOutOfStockTagListener::class,
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

        //
    }
}
