<?php

namespace App\Modules\NonInventoryProductTag\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Tags\Tag;

class ClearArcadiaStockJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 500;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this)]);
    }

    public function handle(): bool
    {
        $productIds = Product::withAnyTags(Tag::findFromStringOfAnyType('ARCADIA DEAL JAN 2023'))
            ->where('quantity', '!=', 0)
            ->get()
            ->pluck('id');

        $query = Inventory::query()->whereIn('product_id', $productIds)
            ->inRandomOrder()
            ->where('quantity', '!=', 0)
            ->limit(100);

        $max_rounds = 5;

        do {
            $inventoryCollection = $query->get();

            $inventoryCollection->each(function (Inventory $inventory) {
                $this->clearToO($inventory);
            });

            $max_rounds--;
        } while ($inventoryCollection->count() > 0 && $max_rounds > 0);

        return true;
    }

    /**
     * @param Inventory $inventory
     */
    private function clearToO(Inventory $inventory): void
    {
        $inventory->refresh();
        if ($inventory->quantity != 0) {
            InventoryService::adjustQuantity(
                $inventory,
                -$inventory->quantity,
                'non_inventory_product_adjustment'
            );
        }
    }
}
