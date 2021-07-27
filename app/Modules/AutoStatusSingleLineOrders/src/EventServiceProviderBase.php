<?php

namespace App\Modules\AutoStatusSingleLineOrders\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto single_line_orders status';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically changes status from "paid" to "single_line_orders" '.
        'if order has only 1 product ordered';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SetStatusSingleLineOrders::class,
        ],

        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrdersCheckEvent\SetStatusSingleLineOrders::class,
        ],
    ];
}
