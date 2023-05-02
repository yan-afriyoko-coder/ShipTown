<?php

namespace App\Modules\Telescope\src;

use App\Events\DailyEvent;
use App\Modules\BaseModuleServiceProvider;

class TelescopeModuleServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Telescope';

    public static string $module_description = ' Telescope module will help you to debug your application. ';

    public static string $settings_link = '/telescope';

    public static bool $autoEnable = true;

    protected $listen = [
        DailyEvent::class => [
            Listeners\DailyEventListener::class
        ],
    ];
}
