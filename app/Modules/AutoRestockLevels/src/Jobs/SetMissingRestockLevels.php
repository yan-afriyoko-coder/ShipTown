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
            ->limit(100)
            ->get();

        if ($collection->isEmpty()) {
            return;
        }

        $collection->each(function (Inventory $inventory) {
            $newRestockLevel = $inventory->quantity;

            if ($newRestockLevel > 3) {
                $newReorderPoint = ceil($newRestockLevel * 0.32);
            } else {
                $newReorderPoint = floor($newRestockLevel - 1);
            }

            $inventory->update([
                'restock_level' => ceil($newRestockLevel),
                'reorder_point' => $newReorderPoint,
            ]);
        });

        self::dispatch()->delay(5);
    }
}
