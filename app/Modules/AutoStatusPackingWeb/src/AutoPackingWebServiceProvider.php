<?php

namespace App\Modules\AutoStatusPackingWeb\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoPackingWebServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto "packing_web" status';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically changes status from "picking" to "packing_web" '.
    'when order is fully picked';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SetPackingWebStatus::class,
        ],
    ];
}
