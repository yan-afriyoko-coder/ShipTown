<?php

namespace App\Modules\AddressLabel\src;

use App\Models\ShippingService;
use App\Modules\BaseModuleServiceProvider;

class AddressLabelServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Address Label';

    /**
     * @var string
     */
    public static string $module_description = 'Allows to generate generic address label for the order';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    public static function enabling(): bool
    {
        $shippingService = new ShippingService();
        $shippingService->code = 'address_label';
        $shippingService->service_provider_class = AddressLabelServiceProvider::class;
        $shippingService->save();

        return true;
    }

    public static function disabling(): bool
    {
        ShippingService::query()->where(['code' => 'address_label'])->delete();
        return true;
    }
}
