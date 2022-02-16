<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->timestamp('created_at', 0)->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('SET NULL');
        });
    }
}
