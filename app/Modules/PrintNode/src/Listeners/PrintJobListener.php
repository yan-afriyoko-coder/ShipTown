<?php

namespace App\Modules\PrintNode\src\Listeners;

use App\Events\PrintJob\PrintJobCreatedEvent;
use App\Modules\PrintNode\src\PrintNode;

class PrintJobListener
{
    public function handle(PrintJobCreatedEvent $event)
    {
        PrintNode::print($event->printJob);
    }
}
