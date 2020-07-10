<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkuDescriptionColumnsToPicklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {

            $table->string('sku_ordered')
                ->after('location_id')
                ->default(' ');

            $table->string('name_ordered')
                ->after('sku_ordered')
                ->default(' ');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('sku_ordered');
            $table->dropColumn('name_ordered');
        });
    }
}
