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

            if ($newRestockLevel > 100) {
                $newReorderPoint = $newRestockLevel * 0.50;
            } elseif ($newRestockLevel > 30) {
                $newReorderPoint = $newRestockLevel * 0.33;
            } elseif ($newRestockLevel > 5) {
                $newReorderPoint = $newRestockLevel * 0.25;
            } else {
                $newReorderPoint = $newRestockLevel - 1;
            }

            $inventory->update([
                'restock_level' => ceil($newRestockLevel),
                'reorder_point' => floor($newReorderPoint),
            ]);
        });

        self::dispatch()->delay(5);
    }
}
