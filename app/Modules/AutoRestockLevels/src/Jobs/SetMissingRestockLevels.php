<?php

namespace App\Modules\AutoRestockLevels\src\Jobs;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SetMissingRestockLevels implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $collection = Inventory::query()
            ->where('quantity', '>', 0)
            ->where('restock_level', '=', 0)
            ->inRandomOrder()
            ->limit(50)
            ->get();

        if ($collection->isEmpty()) {
            return;
        }

        $collection->each(function (Inventory $inventory) {
            $inventory->update([
                'restock_level' => ceil($inventory->quantity),
                'reorder_point' => ceil($inventory->quantity) - 1,
            ]);
        });

        self::dispatch()->delay(5);
    }
}
