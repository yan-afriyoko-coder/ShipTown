<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUniqueKeyToProductsPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DELETE FROM products_prices WHERE id IN (

SELECT id FROM (SELECT min(id) as id

FROM products_prices

GROUP BY product_id, warehouse_id

HAVING count(*) > 1) as tbl)');

        Schema::table('products_prices', function (Blueprint $table) {
            $table->unique(['product_id', 'warehouse_id']);
        });
    }
}
