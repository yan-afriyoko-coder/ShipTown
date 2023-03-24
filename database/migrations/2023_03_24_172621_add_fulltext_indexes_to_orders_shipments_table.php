<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFulltextIndexesToOrdersShipmentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('orders_shipments', function (Blueprint $table) {
            $table->fullText(['shipping_number']);
        });
    }
}
