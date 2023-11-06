<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_products', 13)->default(0)->change();
            $table->decimal('total_shipping', 13)->default(0)->change();
            $table->decimal('total', 13)->default(0)->change();
            $table->decimal('total_discounts', 13)->default(0)->change();
            $table->decimal('total_paid', 13)->default(0)->change();

            $table->decimal('total_order', 13)
                ->storedAs('total_products + total_shipping - total_discounts')
                ->comment('total_products + total_shipping - total_discounts')
                ->after('total_discounts');

            $table->decimal('total_outstanding', 13)
                ->storedAs('total_products + total_shipping - total_discounts - total_paid')
                ->comment('total_products + total_shipping - total_discounts - total_paid')
                ->after('total_discounts')
            ;
        });
    }
};
