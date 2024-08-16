<?php

use App\Models\Inventory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Inventory::query()
            ->whereNull('product_sku')
            ->chunkById(5000, function ($inventories) {
                Inventory::query()
                    ->whereIn('id', $inventories->pluck('id'))
                    ->update(['product_sku' => DB::raw('(SELECT sku FROM products WHERE products.id = inventory.product_id)')]);

                usleep(5000); // 10ms
            });
    }
};
