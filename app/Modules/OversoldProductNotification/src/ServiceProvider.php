<?php

namespace App\Modules\OversoldProductNotification\src;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\OversoldProductNotification\src
 */
class ServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Oversold Product Notification';

    /**
     * @var string
     */
    public static string $module_description = 'Sends email notification for oversold product';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ProductTagAttachedEvent::class => [
            Listeners\OversoldProductAttachedEvent\SendOversoldProductNotificationListener::class
        ]
    ];
}
