<?php


namespace App\Modules\PrintNode\src;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

class PrintNodeServiceProvider extends BaseModuleServiceProvider
{
    protected $listen = [
        OrderShipmentCreatedEvent::class => [

        ]
    ];
}
