<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\StocktakeSuggestion;

class InventoryUpdatedEventListener
{
    public function handle(InventoryUpdatedEvent $event)
    {
        StocktakeSuggestion::query()->where(['inventory_id' => $event->inventory->id])->delete();
    }
}
