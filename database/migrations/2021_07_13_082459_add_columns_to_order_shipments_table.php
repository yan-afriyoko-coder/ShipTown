<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->string('carrier')->after('order_id')->default('');
            $table->string('service')->after('carrier')->default('');
            $table->string('tracking_url')->after('shipping_number')->default('');
            $table->longText('base64_pdf_labels')->after('tracking_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            //
        });
    }
}
