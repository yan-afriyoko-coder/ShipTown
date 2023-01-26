<?php

namespace App\Modules\NonInventoryProductTag\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Tags\Tag;

class ClearArcadiaStockJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function handle(): bool
    {
        $productIds = Product::withAllTags(Tag::findFromStringOfAnyType('ARCADIA DEAL JAN 2023'))
            ->where('quantity', '!=', 0)
            ->get()
            ->pluck('id');

//        Inventory::query()->whereIn('product_id', $productIds)
//            ->where('quantity', '!=', 0)
//            ->get()
//            ->each(function (Inventory $inventory) {
//                InventoryService::adjustQuantity($inventory, -$inventory->quantity, 'non_inventory_product_adjustment');
//            });

        return true;
    }
}
