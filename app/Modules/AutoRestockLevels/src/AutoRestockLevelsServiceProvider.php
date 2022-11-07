<?php

namespace App\Modules\AutoRestockLevels\src;

use App\Events\EveryMinuteEvent;
use App\Events\HourlyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\SyncRequestedEvent;
use App\Models\Inventory;
use App\Modules\BaseModuleServiceProvider;

class AutoRestockLevelsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto Restock Levels';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically manages stock levels based on inventory and sales';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],
    ];
}
