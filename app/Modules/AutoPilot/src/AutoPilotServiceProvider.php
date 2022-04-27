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
    public static string $module_name = 'Automation - Packer Clear';

    /**
     * @var string
     */
    public static string $module_description = 'Clears Packer assignment after 12h of inactivity on order';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class,
        ],
    ];
}
