<?php

namespace App\Modules\Rmsapi\src;

use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
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
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryFiveMinutesEventListener::class,
        ],

        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        EveryHourEvent::class => [
            Listeners\HourlyEventListener::class,
        ],
    ];
}
