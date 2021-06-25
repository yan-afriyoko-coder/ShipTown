<?php


namespace App\Modules\Maintenance\src;

use App\Events\DailyEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase
 * @package App\Modules\Maintenance\src
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'Maintenance';

    /**
     * @var string
     */
    public string $module_description = 'Basic maintenance tasks';

    /**
     * @var array
     */
    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEvent\RunDailyMaintenanceJobsListener::class,
        ],
    ];
}
