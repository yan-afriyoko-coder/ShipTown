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
use App\Modules\BaseModuleServiceProvider;

/**
 * Class Api2cartServiceProvider.
 */
class Api2cartServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'eCommerce - Api2cart Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Module provides connectivity to eCommerce platforms using api2cart.com';

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
        SyncRequestedEvent::class => [Listeners\SyncRequestedEventListener::class],

        HourlyEvent::class => [Listeners\HourlyEventListener::class],

        DailyEvent::class => [Listeners\DailyEventListener::class],

        ProductPriceUpdatedEvent::class => [Listeners\ProductPriceUpdatedEventListener::class],

        ProductTagAttachedEvent::class => [Listeners\ProductTagAttachedEventListener::class],

        ProductTagDetachedEvent::class => [Listeners\ProductTagDetachedEventListener::class],

        InventoryUpdatedEvent::class => [Listeners\InventoryUpdatedEventListener::class],

        OrderUpdatedEvent::class => [Listeners\OrderUpdatedEventListener::class]
    ];

    public function boot()
    {
        parent::boot();

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
