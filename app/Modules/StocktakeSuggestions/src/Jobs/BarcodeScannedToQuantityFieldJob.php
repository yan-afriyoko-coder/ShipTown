<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BarcodeScannedToQuantityFieldJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Warehouse $warehouse_id;

    public function __construct(Warehouse $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    public function handle(): bool
    {
        $reason = 'possible barcode scanned into quantity field';
        $points = 50;

        StocktakeSuggestion::query()->where(['reason' => $reason])->delete();

        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, points, reason, created_at, updated_at)
            SELECT id, ?, ?, NOW(), NOW()
            FROM inventory
            WHERE warehouse_id = ?
              AND quantity > 100000000
            ORDER BY quantity DESC
            LIMIT 100
        ', [$points, $reason, $this->warehouse_id]);

        return true;
    }
}
