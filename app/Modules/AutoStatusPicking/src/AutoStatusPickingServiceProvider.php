<?php

namespace App\Modules\AutoStatusPicking\src;

use App\Events\HourlyEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class AutoStatusPickingServiceProvider.
 */
class AutoStatusPickingServiceProvider extends BaseModuleServiceProvider
{
    public bool $autoEnable = false;

    /**
     * @var string
     */
    public string $module_name = 'Auto "picking" status';

    /**
     * @var string
     */
    public string $module_description = 'Automatically moves batch of orders "paid" to "picking" status. '.
    'It prioritize old orders';

    /**
     * @var string[][]
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEvent\RefillPickingIfEmpty::class,
        ],
    ];
}
