<?php

namespace App\Modules\InventoryMovementsStatistics\src;

use App\Events\Every10MinuteEvent;
use App\Events\HourlyEvent;
use App\Events\InventoryMovementCreatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateStatisticsTableJob;
use Illuminate\Support\Facades\DB;

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

        Every10MinuteEvent::class => [
            Listeners\Every10MinuteEventListener::class,
        ],

        InventoryMovementCreatedEvent::class => [
            Listeners\InventoryMovementCreatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }

    public static function disabling(): bool
    {
        return false;
    }
}
