<?php

namespace App\Modules\MagentoApi\src;

use App\Events\EveryTenMinutesEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Magento 2.0 API Price Sync';

    public static string $module_description = 'Module provides ability to sync full & special prices with Magento 2';

    public static string $settings_link = '/settings/modules/magento-api';

    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        ProductTagAttachedEvent::class => [
            Listeners\ProductTagAttachedEventListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEventListener::class,
        ],
    ];
}
