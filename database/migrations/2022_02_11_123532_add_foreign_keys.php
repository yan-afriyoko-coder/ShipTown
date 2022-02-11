<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_automations_actions', function (Blueprint $table) {
            $table->foreign('automation_id')
                ->references('id')
                ->on('modules_automations')
                ->onDelete('CASCADE');
        });

        Schema::table('modules_automations_conditions', function (Blueprint $table) {
            $table->foreign('automation_id')
                ->references('id')
                ->on('modules_automations')
                ->onDelete('CASCADE');
        });

        Schema::table('picks', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('SET NULL');
        });

        Schema::dropIfExists('telescope_entries');
        Schema::dropIfExists('telescope_entries_tags');
        Schema::dropIfExists('telescope_monitoring');
    }
}
