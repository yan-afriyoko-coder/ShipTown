<?php

namespace App\Modules\TwoFactorAuth\src;

use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\ShipmentConfirmationEmail\src
 */
class TwoFactorAuthServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Two Factor Authentication';

    /**
     * @var string
     */
    public static string $module_description = 'Provides 2FA trough user email';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];
}
