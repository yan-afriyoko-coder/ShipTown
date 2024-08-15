<?php

use App\Models\ProductPrice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ProductPrice::query()
            ->where('inventory_id', null)
            ->chunk(1000, function ($records) {
                ProductPrice::query()
                    ->whereIn('id', $records->pluck('id'))
                    ->update([
                        'inventory_id' => DB::raw('(SELECT inventory_id FROM inventory WHERE inventory.warehouse_id = warehouse_id AND inventory.product_id = product_id)'),
                    ]);

                usleep(10000); // 10ms
            });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->nullable(false)->change();

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->cascadeOnDelete();
        });
    }
};
