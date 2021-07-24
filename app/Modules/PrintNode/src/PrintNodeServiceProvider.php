<?php


namespace App\Modules\PrintNode\src;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

class PrintNodeServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var bool
     */
    public bool $autoEnable = true;

    protected $listen = [
        OrderShipmentCreatedEvent::class => [

        ]
    ];
}
