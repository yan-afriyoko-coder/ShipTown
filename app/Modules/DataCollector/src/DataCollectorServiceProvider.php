<?php

namespace App\Modules\DataCollector\src;

use App\Events\Every10minEvent;
use App\Models\ShippingService;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class DataCollectorServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Data Collector Services';

    /**
     * @var string
     */
    public static string $module_description = 'Provides bulk actions for data collections';

    /**
     * @var string
     */
    public static string $settings_link = '/modules/data-collector';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Every10minEvent::class => [
            Listeners\Every10minEventListener::class,
        ],
    ];
}
