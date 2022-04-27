<?php

namespace App\Modules\FireActiveOrderCheckEvent\src;

use App\Events\HourlyEvent;
use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class ActiveOrderCheckEventServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Active Order Checks';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically fires ActiveOrderCheck event 5 minutes ' .
        'after the order is created';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderCreatedEvent::class => [
            Listeners\OrderCreatedListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ]
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
