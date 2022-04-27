<?php

namespace App\Modules\OrderTotals\src;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Models\OrderProduct;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryReservations\src\Listeners\OrderProductCreatedListener;

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
    public bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    public static function disabling(): bool
    {
        return false;
    }
}
