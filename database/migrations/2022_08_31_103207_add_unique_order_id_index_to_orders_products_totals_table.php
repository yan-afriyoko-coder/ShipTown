<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueOrderIdIndexToOrdersProductsTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('
            DELETE FROM orders_products_totals WHERE ID IN (
                SELECT ID FROM (
                    SELECT min(b.id) as ID

                    FROM orders_products_totals as b

                    GROUP BY b.order_id

                    HAVING count(*)  > 1
                ) as TBL
            )

        ');

        \App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob::dispatch();
        \App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob::dispatch();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_products_totals', function (Blueprint $table) {
            //
        });
    }
}
