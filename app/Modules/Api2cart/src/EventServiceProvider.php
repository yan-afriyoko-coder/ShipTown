<?php

namespace App\Modules\Api2cart\src;

use App\Events\DailyEvent;
use App\Events\HourlyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Events\ProductPrice\ProductPriceUpdatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\Api2cart\src\Listeners\HourlyEvent\DispatchSyncProductsJobListener;
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
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DispatchImportOrdersJobsListener::class,
        ],

        HourlyEvent::class => [
            DispatchSyncProductsJobListener::class,
        ],

        DailyEvent::class => [
            Listeners\DailyEvent\RunResyncLastDayJobListener::class,
            Listeners\DailyEvent\RunResyncSyncErrorTaggedJobListener::class,
            Listeners\DailyEvent\RunCheckFailedTaggedJobListener::class,
        ],

        ProductPriceUpdatedEvent::class => [
            Listeners\ProductPriceUpdatedEvent\AddNotSyncedTagListener::class,
        ],

        ProductTagAttachedEvent::class => [
            Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEvent\AddNotSyncedTagListener::class,
        ],
    ];
}
