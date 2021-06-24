<?php

namespace App\Modules\AutoStatusPackingWarehouse\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\ModuleServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ModuleServiceProvider
{
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrdersCheckEvent\SetStatusPackingWarehouseListener::class,
        ],
    ];
}
