<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\StocktakeSuggestion;
use Illuminate\Support\Facades\Bus;

class InventoryUpdatedEventListener
{
    public function handle(InventoryUpdatedEvent $event)
    {
        Bus::dispatchAfterResponse(function () use ($event) {
            StocktakeSuggestion::query()->where(['inventory_id' => $event->inventory->id])->delete();
        });
    }
}
