<?php

namespace App\Modules\Api2cart\src;

use App\Events\DailyEvent;
use App\Events\HourlyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Events\Product\ProductPriceUpdatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\Api2cart\src\Listeners\HourlyEvent\DispatchSyncProductsJobListener;
use App\Modules\BaseModuleServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class Api2cartServiceProvider.
 */
class Api2cartServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'eCommerce Integration (Api2cart)';

    /**
     * @var string
     */
    public static string $module_description = 'Module provides connectivity to eCommerce platforms. It uses api2cart.com';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

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

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SyncStatusListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
