<?php

namespace App\Modules\AutoStatusPicking\src;

use App\Events\HourlyEvent;
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
    public string $module_name = 'Auto "picking" status';

    /**
     * @var string
     */
    public string $module_description = 'Automatically moves batch of orders "paid" to "picking" status. ' .
    'It prioritize old orders';

    /**
     * @var string[][]
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEvent\RefillStatusPickingListener::class,
        ],
    ];
}
