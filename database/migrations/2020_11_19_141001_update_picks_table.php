<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picks', function (Blueprint $table) {
            $table->bigInteger('user_id')
                ->unsigned()
                ->nullable(true)
                ->after('id');
            $table->decimal('quantity_picked')
                ->default(0)
                ->after('name_ordered');
            $table->decimal('quantity_skipped_picking')
                ->default(0)
                ->after('quantity_picked');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::table('picks', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('quantity_picked');
            $table->dropColumn('quantity_skipped_picking');
        });
    }
}
