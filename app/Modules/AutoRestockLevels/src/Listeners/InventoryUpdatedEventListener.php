<?php

namespace App\Modules\AutoRestockLevels\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\AutoRestockLevels\src\Jobs\SetMissingRestockLevels;

class InventoryUpdatedEventListener
{
    public function handle(InventoryUpdatedEvent $event)
    {
        SetMissingRestockLevels::dispatchNow($event->inventory);
    }
}
