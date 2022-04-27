<?php

namespace App\Modules\DpdIreland\src;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Models\ShippingService;
use App\Modules\AddressLabel\src\Services\AddressLabelShippingService;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\DpdIreland\src\Services\NextDayShippingService;

/**
 * Class EventServiceProviderBase.
 */
class DpdIrelandServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'DPD Ireland Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Provides seamless integration with DPD Ireland';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];


    public static function enabling(): bool
    {
        ShippingService::query()->updateOrCreate([
            'code' => 'dpd_label',
        ], [
            'service_provider_class' => Services\NextDayShippingService::class,
        ]);

        ShippingService::query()->updateOrCreate([
            'code' => 'dpd_irl_next_day',
        ], [
            'service_provider_class' => Services\NextDayShippingService::class,
        ]);

        return true;
    }

    public static function disabling(): bool
    {
        ShippingService::query()->where(['code' => 'dpd_irl_next_day'])->delete();
        ShippingService::query()->where(['code' => 'dpd_label'])->delete();

        return true;
    }
}
