<?php

namespace App\Modules\AutoStatusPicking\src;

use App\Events\HourlyEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;
use PhpParser\Node\Expr\List_;

/**
 * Class AutoStatusPickingServiceProvider.
 */
class AutoStatusPickingServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automation - Auto "picking" refilling';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically moves batch of orders "paid" to "picking" status. '.
    'It prioritize old orders';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * @var string[][]
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEvent\RefillPickingIfEmpty::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\RefillPickingIfEmpty::class,
        ],
    ];
}
