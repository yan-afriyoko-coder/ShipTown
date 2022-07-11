<?php


namespace App\Modules\Reports\src;

use App\Events\HourlyEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 *
 */
class ReportsServiceProvider extends BaseModuleServiceProvider
{

    /**
     * @var string
     */
    public static string $module_name = '.CORE - Reports';

    /**
     * @var string
     */
    public static string $module_description = 'Provides reports under menu section';

    /**
     * @var string
     */
    public static string $settings_link = '';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ]
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
