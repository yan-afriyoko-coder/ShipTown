<?php

namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Maintenance';

    /**
     * @var string
     */
    public static string $module_description = 'Daily Maintenance Jobs';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEvent\RunDailyMaintenanceJobsListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
