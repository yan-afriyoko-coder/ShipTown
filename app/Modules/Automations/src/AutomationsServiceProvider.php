<?php

namespace App\Modules\Automations\src;

use App\Events\DailyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutomationsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automations';

    /**
     * @var string
     */
    public static string $module_description = 'Provides user specified automations';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderCreatedEvent::class => [
            Listeners\EventsListener::class
        ],

        OrderUpdatedEvent::class => [
            Listeners\EventsListener::class
        ],

        InventoryUpdatedEvent::class => [
            Listeners\EventsListener::class
        ],
    ];
}
