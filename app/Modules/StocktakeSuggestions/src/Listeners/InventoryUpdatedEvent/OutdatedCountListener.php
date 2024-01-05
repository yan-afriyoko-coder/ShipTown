<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners\InventoryUpdatedEvent;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\StocktakeSuggestion;
use App\Modules\StocktakeSuggestions\src\Models\StocktakeSuggestionsConfiguration;
use Illuminate\Support\Facades\Cache;

class OutdatedCountListener
{
    public function handle(InventoryUpdatedEvent $event): void
    {
        $inventory = $event->inventory;

        if ($inventory->quantity === 0.00 || $inventory->first_movement_at === null) {
            return;
        }

        $min_count_date = Cache::get('stocktake_suggestions_min_count_date', function () {
            /** @var StocktakeSuggestionsConfiguration $configuration */
            $configuration = StocktakeSuggestionsConfiguration::query()->firstOrCreate();

            Cache::set('stocktake_suggestions_min_count_date', $configuration->min_count_date, 60);

            return $configuration->min_count_date;
        });

        if ($min_count_date === null || $inventory->first_movement_at->isAfter($min_count_date)) {
            return;
        }

        if ($inventory->last_counted_at === null || $inventory->last_counted_at->isBefore($min_count_date)) {
            StocktakeSuggestion::query()->upsert([
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'points' => 1,
                'reason' => 'outdated count',
            ], ['inventory_id', 'reason']);
        }
    }
}
