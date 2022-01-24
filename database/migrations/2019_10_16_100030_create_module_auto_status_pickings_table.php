<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleAutoStatusPickingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('module_auto_status_pickings')) {
            return;
        }

        Schema::create('module_auto_status_pickings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')
                ->nullable(false)
                ->default(true);
            $table->timestamps();
        });
    }
}
