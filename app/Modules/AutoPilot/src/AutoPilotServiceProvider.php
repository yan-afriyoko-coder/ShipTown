<?php

namespace App\Modules\AutoPilot\src;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\Listeners\ClearPackerIdListener;
use App\Modules\BaseModuleServiceProvider;

class AutoPilotServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'AutoPilot';

    /**
     * @var string
     */
    public string $module_description = 'Clears Packer assignment after 12h of inactivity on order';

    /**
     * @var array
     */
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class,
        ],
    ];
}
