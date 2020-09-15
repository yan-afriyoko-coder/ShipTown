<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickIdForeignKeyToPickRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pick_requests', function (Blueprint $table) {
            $table->foreign('pick_id')
                ->references('id')
                ->on('picks')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pick_requests', function (Blueprint $table) {
            $table->dropForeign(['pick_id']);
        });
    }
}
