<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('product_id')->unsigned()->nullable(true);
            $table->string('sku_ordered');
            $table->string('name_ordered');
            $table->decimal('quantity_required');
            $table->bigInteger('picker_user_id')->unsigned()->nullable(true);
            $table->timestamp('picked_at');

            $table->softDeletes();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picks');
    }
}
