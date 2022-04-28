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
    public static string $module_name = 'Automation - Packer Collision';

    /**
     * @var string
     */
    public static string $module_description = 'Locks orders from being assigned to two packers at the same time';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        HourlyEvent::class => [
            ClearPackerIdListener::class,
        ],
    ];
}
