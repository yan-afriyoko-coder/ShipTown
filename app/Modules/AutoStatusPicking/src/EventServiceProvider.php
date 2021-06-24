<?php

namespace App\Modules\AutoStatusPicking\src;

use App\Events\HourlyEvent;
use App\Modules\ModuleServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ModuleServiceProvider
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
