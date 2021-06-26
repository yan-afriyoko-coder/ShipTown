<?php

namespace App\Modules\AutoStatusPackingWarehouse\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoPackingWarehouseServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'Auto packing_warehouse status';

    /**
     * @var string
     */
    public string $module_description = 'Changes status from "paid" to "packing_warehouse" '.
    'if can fulfill from location 99';

    /**
     * @var array
     */
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrdersCheckEvent\SetStatusPackingWarehouseListener::class,
        ],
    ];
}
