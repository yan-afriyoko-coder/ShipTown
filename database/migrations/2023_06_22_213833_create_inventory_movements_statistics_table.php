<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->index();
            $table->foreignId('product_id')->index();
            $table->foreignId('warehouse_id')->index();
            $table->decimal('quantity_sold', 10)->index();
            $table->string('description')->index();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO inventory_movements_statistics (description, inventory_id, product_id, warehouse_id, quantity_sold)
            SELECT
              'sold_last_7days' as description,
              inventory_id,
              product_id,
              warehouse_id,
              sum(quantity_delta)

            FROM `inventory_movements`
            WHERE
              `type` = 'sale'
              AND `created_at` > DATE_ADD(now(), INTERVAL -7 DAY)

            GROUP BY
              inventory_id,
              product_id,
              warehouse_id

        ");
    }
};
