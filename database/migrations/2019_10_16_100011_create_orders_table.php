<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->string('order_number')->unique();
            $table->string('status_code')->default('');
            $table->boolean('is_active')->nullable(false)->default(0);
            $table->boolean('is_editing')->default(0);
            $table->decimal('total_products')->default(0);
            $table->decimal('total_shipping')->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('total_paid')->default(0);
            $table->string('shipping_method_code')->default('')->nullable(true);
            $table->string('shipping_method_name')->default('')->nullable(true);
            $table->timestamp('order_placed_at')->useCurrent()->nullable();
            $table->timestamp('order_closed_at')->nullable();
            $table->integer('product_line_count')->default(0);
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->unsignedBigInteger('packer_user_id')->nullable();
            $table->decimal('total_quantity_ordered', 10, 2)->default(0);
            $table->decimal('total_quantity_to_ship')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('shipping_address_id')
                ->on('order_addresses')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('packer_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_discounts', 10)->default(0)
                ->after('total_paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
