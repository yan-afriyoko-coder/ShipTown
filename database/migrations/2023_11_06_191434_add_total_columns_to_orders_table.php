<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropColumns('orders', ['is_fully_paid']);

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 13)->default(0)->change();
            $table->decimal('total_products', 13)->nullable()->default(null)->change();
            $table->decimal('total_shipping', 13)->default(0)->change();
            $table->decimal('total_discounts', 13)->default(0)->change();

            $table->boolean('is_fully_paid')
                ->storedAs('total_products + total_shipping - total_discounts - total_paid < 0.01')
                ->comment('total_products + total_shipping - total_discounts - total_paid < 0.01')
                ->after('is_editing');

            $table->decimal('total_order', 13)
                ->storedAs('total_products + total_shipping - total_discounts')
                ->comment('total_products + total_shipping - total_discounts')
                ->after('total_discounts');

            $table->decimal('total_paid', 13)->default(0)->change();

            $table->decimal('total_outstanding', 13)
                ->storedAs('total_products + total_shipping - total_discounts - total_paid')
                ->comment('total_products + total_shipping - total_discounts - total_paid')
                ->after('total_paid');

            $table->string('custom_unique_reference_id')->unique()->nullable()->after('updated_at');
        });
    }
};
