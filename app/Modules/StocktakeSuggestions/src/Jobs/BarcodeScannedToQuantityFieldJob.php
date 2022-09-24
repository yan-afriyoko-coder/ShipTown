<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BarcodeScannedToQuantityFieldJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function handle(): bool
    {
        $reason = 'possible barcode scanned into quantity field';
        $points = 50;

        StocktakeSuggestion::query()->where(['reason' => $reason])->delete();

        $inventory = Inventory::query()
            ->where('quantity', '>', 100000000)
            ->get(['id'])
            ->collect();

        StocktakeSuggestion::query()->insert(
            $inventory->map(function (Inventory $inventory) use ($reason, $points) {
                return [
                    'inventory_id' => $inventory->id,
                    'points' => $points,
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

        return true;
    }
}
