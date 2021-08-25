<?php

use App\Models\Configuration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('configurations')) {
            return;
        }

        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('');
            $table->timestamps();
        });

        Configuration::create([]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
