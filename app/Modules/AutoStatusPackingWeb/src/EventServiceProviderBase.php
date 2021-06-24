<?php

namespace App\Modules\AutoStatusPackingWeb\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase
 * @package App\Providers
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'Auto "packing_web" status';

    /**
     * @var string
     */
    public string $module_description = 'Automatically changes status from "picking" to "packing_web" ' .
    'when order is fully picked';

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SetPackingWebStatus::class,
        ],
    ];
}
