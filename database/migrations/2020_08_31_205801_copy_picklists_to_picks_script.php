<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyPicklistsToPicksScript extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DROP guineys_picks
//INSERT INTO guineys_picks (
//        product_id,
//        sku_ordered,
//        name_ordered,
//        quantity_required,
//        picker_user_id,
//        picked_at,
//        deleted_at,
//        created_at,
//        updated_at
//    )
//
//SELECT
//product_id,
//sku_ordered,
//name_ordered,
//quantity_requested,
//picker_user_id,
//picked_at,
//deleted_at,
//created_at,
//updated_at
//
//FROM guineys_picklists


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picks_script', function (Blueprint $table) {
            //
        });
    }
}
