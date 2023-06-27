<?php

namespace App\Modules\InventoryMovementsStatistics\src;

use App\Events\EveryMinuteEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class InventoryMovementsStatisticsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Movements Statistics';

    /**
     * @var string
     */
    public static string $module_description = 'Statistics like "Quantity sold in last 7 days"';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEventListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
