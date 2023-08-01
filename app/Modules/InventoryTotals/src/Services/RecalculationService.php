<?php

namespace App\Modules\InventoryTotals\src\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RecalculationService
{
    public static function updateProductTotals($product_id): void
    {
        $inventory = Inventory::query()
            ->where(['product_id' => $product_id])
            ->select([
                'product_id',
                DB::raw('sum(quantity) as quantity'),
                DB::raw('sum(quantity_reserved) as quantity_reserved')
            ]);

        Product::query()->updateOrCreate([
                'id' => $product_id
            ], [
                'quantity' => $inventory->quantity,
                'quantity_reserved' => $inventory->quantity_reserved,
            ]);
    }
}
