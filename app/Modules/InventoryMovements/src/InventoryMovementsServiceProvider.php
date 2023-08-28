<?php

namespace App\Modules\InventoryMovements\src;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\Api2cart\src\Listeners\DailyEventListener;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;

/**
 * Class EventServiceProviderBase.
 */
class InventoryMovementsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Movements';

    /**
     * @var string
     */
    public static string $module_description = 'Monitor inventory movements and ensure inventory quantities quantities are correct.';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        DailyEventListener::class => [
            Listeners\DailyEventListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
