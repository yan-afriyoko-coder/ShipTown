<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickedAtToPicklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->timestamp('picked_at')
                ->after('quantity_picked')
                ->nullable(true);
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
            $table->dropColumn('picked_at');
        });
    }
}
