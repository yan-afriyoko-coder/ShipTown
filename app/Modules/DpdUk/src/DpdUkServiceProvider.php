<?php

namespace App\Modules\DpdUk\src;

use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class DpdUkServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'DPD UK Integration';

    /**
     * @var string
     */
    public static string $module_description = '';

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

    ];
}
