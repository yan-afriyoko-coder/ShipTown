<?php

namespace App\Modules\Rmsapi\src;

use App\Events\DailyEvent;
use App\Events\Every10minEvent;
use App\Events\HourlyEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

class RmsapiModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'eCommerce - RMSAPI Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Provides connectivity to Microsoft RMS 2.0';

    /**
     * @var string
     */
    public static string $settings_link = '/admin/settings/rmsapi';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Every10minEvent::class => [
            Listeners\Every10minEventListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ],
    ];
}
