<?php

namespace App\Modules\StockControl\src;

use App\Events\OrderProduct\OrderProductShipmentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\ShipmentConfirmationEmail\src
 */
class StockControlServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Stock Control';

    /**
     * @var string
     */
    public static string $module_description = 'Increase \ Decrease inventory when product shipped';

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
        OrderProductShipmentCreatedEvent::class => [
            Listeners\OrderProductShipmentCreatedListener::class
        ]
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
