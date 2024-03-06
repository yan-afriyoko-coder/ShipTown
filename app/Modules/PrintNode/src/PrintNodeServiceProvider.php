<?php


namespace App\Modules\PrintNode\src;

use App\Events\ShippingLabel\ShippingLabelCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 *
 */
class PrintNodeServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Printer - PrintNode Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Print labels directly to your own printer';

    /**
     * @var string
     */
    public static string $settings_link = '/admin/settings/printnode';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * @var array
     */
    protected $listen = [
        ShippingLabelCreatedEvent::class => [
            Listeners\PrintShippingLabelListener::class,
        ],
    ];
}
