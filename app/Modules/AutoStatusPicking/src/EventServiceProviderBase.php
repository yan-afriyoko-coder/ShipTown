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
     * @var string[][]
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEvent\RefillStatusPickingListener::class,
        ],
    ];
}
