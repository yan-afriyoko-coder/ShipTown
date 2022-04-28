<?php

namespace App\Modules\OversoldProductNotification\src;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\OversoldProductNotification\src
 */
class OversoldProductNotificationServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automation - Oversold Product Notification';

    /**
     * @var string
     */
    public static string $module_description = 'Sends email  when "oversold" tag attached to product';

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
        ProductTagAttachedEvent::class => [
            Listeners\ProductAttachedEvent\OversoldTagListener::class
        ]
    ];
}
