<?php

namespace App\Modules\OrderStatus\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderStatus\OrderStatusUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class OrderStatusServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Order Status';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically manages order Active & On Hold when status code changed';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEventListener::class,
        ],

        OrderStatusUpdatedEvent::class => [
            Listeners\OrderStatusUpdatedEventListener::class,
        ]
    ];
}
