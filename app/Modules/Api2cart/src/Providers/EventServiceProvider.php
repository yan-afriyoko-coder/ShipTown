<?php

namespace App\Modules\Api2cart\src\Providers;

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
        // ProductPrice
        \App\Events\ProductPrice\ProductPriceUpdatedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductPriceUpdatedEvent\AddNotSyncedTagListener::class,
        ],

        // ProductTag
        \App\Events\Product\ProductTagAttachedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener::class,
        ],

        \App\Events\Product\ProductTagDetachedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener::class,
        ],

        \App\Events\Inventory\InventoryUpdatedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\InventoryUpdatedEvent\AddNotSyncedTagListener::class,
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
