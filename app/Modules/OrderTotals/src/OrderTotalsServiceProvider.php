<?php

namespace App\Modules\OrderTotals\src;

use App\Events\HourlyEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\OversoldProductNotification\src
 */
class OrderTotalsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Order Totals';

    /**
     * @var string
     */
    public static string $module_description = 'Updates order totals like total_quantity_to_ship';

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
            Listeners\HourlyEventListener::class
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedEventListener::class
        ],

        OrderProductCreatedEvent::class => [
            Listeners\OrderProductCreatedEventListener::class
        ],

        OrderProductUpdatedEvent::class => [
            Listeners\OrderProductUpdatedEventListener::class
        ]
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
