<?php

namespace App\Modules\InventoryMovementsStatistics\src;

use App\Events\EveryMinuteEvent;
use App\Events\HourlyEvent;
use App\Events\InventoryMovementCreatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateLast28DaysTableJob;
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

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        InventoryMovementCreatedEvent::class => [
            Listeners\InventoryMovementCreatedEventListener::class,
        ],

        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        RepopulateLast28DaysTableJob::dispatch();

        return parent::enabling();
    }

    public static function disabling(): bool
    {
        return false;
    }
}
