<?php

namespace App\Modules\AutoStatusReady\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoStatusReadyServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto "complete" status';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically changes status to "complete" when order is fully packed';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],
    ];
}
