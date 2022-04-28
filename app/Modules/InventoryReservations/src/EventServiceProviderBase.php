<?php

namespace App\Modules\InventoryReservations\src;

use App\Events\HourlyEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryReservations\src\Listeners\OrderProductUpdatedEvent\UpdateQuantityReservedListener;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Reservations';

    /**
     * @var string
     */
    public static string $module_description = 'Reserves stock for open orders. Is using location 999';

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
        HourlyEvent::class => [
            Listeners\HourlyEvent\RunRecalculateQuantityReservedJobListener::class,
        ],

        OrderProductUpdatedEvent::class => [
            UpdateQuantityReservedListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],

        OrderProductCreatedEvent::class => [
            Listeners\OrderProductCreatedListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
