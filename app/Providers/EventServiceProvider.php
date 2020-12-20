<?php

namespace App\Providers;

use App\Listeners\Inventory\Created\AddToProductsQuantityReservedListener;
use App\Listeners\Inventory\Created\AddToProductTotalQuantityListener;
use App\Listeners\Inventory\Deleted\DeductFromProductsQuantityReservedListener;
use App\Listeners\Inventory\Deleted\DeductFromProductTotalQuantityListener;
use App\Listeners\Inventory\Updated\UpdateProductsQuantityReservedListener;
use App\Listeners\Inventory\Updated\UpdateProductTotalQuantityListener;
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
        \App\Events\Product\CreatedEvent::class => [
            \App\Listeners\Product\Created\PublishSnsNotificationListener::class,
        ],

        \App\Events\Product\UpdatedEvent::class => [
            \App\Listeners\Product\Updated\AttachTagsListener::class,
            \App\Listeners\Product\ProductUpdatedEventListener::class,
        ],

        // Order
        \App\Events\Order\CreatedEvent::class => [
            PublishSnsNotificationListener::class
        ],

        \App\Events\Order\UpdatedEvent::class => [
            \App\Listeners\Order\Updated\PublishSnsNotificationListener::class,
            \App\Listeners\Order\Updated\CheckAndMarkPaidListener::class,
            \App\Listeners\Order\Updated\ChangeToPackingWebStatusListener::class,
            \App\Listeners\Order\Updated\ChangeStatusToReadyIfPackedListener::class,
        ],

        \App\Events\Order\StatusChangedEvent::class => [
            \App\Listeners\Order\StatusChanged\UpdateClosedAt::class,
        ],

        // Inventory
        \App\Events\Inventory\CreatedEvent::class => [
            AddToProductTotalQuantityListener::class,
            AddToProductsQuantityReservedListener::class,
        ],

        \App\Events\Inventory\UpdatedEvent::class => [
            UpdateProductTotalQuantityListener::class,
            UpdateProductsQuantityReservedListener::class
        ],

        \App\Events\Inventory\DeletedEvent::class => [
            DeductFromProductTotalQuantityListener::class,
            DeductFromProductsQuantityReservedListener::class
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
