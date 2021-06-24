<?php

namespace App\Modules\AutoStatusSingleLineOrders\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase
 * @package App\Providers
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrdersCheckEvent\SetStatusSingleLineOrders::class,
        ],
    ];
}
